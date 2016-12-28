<?php

    $is_searched = false;
    $bill_amount = null;
    $tip_percent = null;
    $split_num = null;

    if(isset($_POST["billtotal"]) && isset($_POST["percent"])) {
        $bill_amount = floatval($_POST["billtotal"]);
        $tip_percent = floatval($_POST["percent"]);
        $split_num = intval($_POST["split"]);
        $is_searched = true;
    }

    insert_top_page();
    if ($is_searched && is_valid_amount($bill_amount, '/^[0-9]+(\.[0-9][0-9])?$/')
        && is_valid_amount($split_num, "/^[0-9]{1,}$/")) {
        insert_results($bill_amount, $tip_percent, $split_num);
    } else if ($is_searched && !is_valid_amount($bill_amount, '/^[0-9]+(\.[0-9][0-9])?$/')) {
        display_error("Bill amount", "A dollar value + cents");
    } else if ($is_searched && !is_valid_amount($split_num, "/^[0-9]{1,}$/")) {
        display_error("Split", "A whole number");
    }
    insert_bottom_page();

    function insert_results($bill_amount, $tip_percent, $split) {
        $tip = $bill_amount * ($tip_percent/100);
        $bill_total = $bill_amount + $tip;
        ?>
        <div id="results">
            <p>
                Total Tip: $<?= number_format($tip, 2, '.', '') ?>
            </p>
            <p>
                Total: $<?= number_format($bill_total, 2, '.', '') ?>
            </p>
            <?php
            if ($split > 1) {
                $split_tip = $tip / $split;
                $split_total = ($bill_amount / $split) + $split_tip;
                ?>
                <p>
                    Each Person Tips: $<?= number_format($split_tip, 2, '.', '') ?>
                </p>
                <p>
                    Each Person Pays: $<?= number_format($split_total, 2, '.', '') ?>
                </p>
            <?php
            }
            ?>
        </div>
    <?php
    }

    function display_error($item, $num) { ?>
        <div id="error">
            <p>
                 <?= $item ?> must be:
                <ul>
                    <li><?= $num ?></li>
                    <li>Greater than 0</li>
                    <li>Non-Empty</li>
                    <li>Non-Negative</li>
                </ul>
            </p>
        </div>
    <?php
    }

    function is_valid_amount($amount, $regex) {
        if (preg_match($regex, $amount) && floatval($amount) > 0.0) {
            return true;
        }
        return false;
    }

    function insert_top_page() { ?>
        <!DOCTYPE html>
        <html lang = "en">
        <head>
            <title>Dustin Langner's Tip Calculator</title>
            <meta charset = "UTF-8" />
            <link href="tip-calculator.css" rel="stylesheet" type="text/css">
        </head>
        <body>

        <div id="calc-container">
        <h1>TipThem</h1>
        <form id="calculateform" action="calculate-tip.php" method="post">
        <fieldset>
        <div id="bill-amount">
            <strong>Bill Amount: $ </strong>
            <input name="billtotal" type="text" size="15" autofocus="autofocus"
            <?php if (isset($_POST['billtotal'])) {
                print 'value=' . $_POST['billtotal'];
            } ?> />
        </div>
        <div id="tip-percent">
            <strong>Tip Percentage: </strong>
            <br />
            <?php
            for ($i = 2; $i < 5; $i++) { ?>
                <input name="percent" type="radio" value="<?= $i * 5?>"
                       <?php if (isset($_POST['percent']) && $_POST['percent'] == $i * 5 || !isset($_POST["percent"])) {
                           echo 'checked';
                       } ?> />
                <label><?= $i * 5?>%</label>
                <?php
            }
            ?>
        </div>
        <div id="split-bill">
            <strong class="split-word">Split Bill Among: </strong>
            <input name="split" type="text" size="15"
            <?php if (isset($_POST['split'])) {
                print 'value=' . $_POST['split'];
            } else {
                print 'value=1';
            } ?> />
            <strong class="split-word"> person(s) </strong>

        </div>
        <div id="submit">
            <input type="submit" value="Calculate Tip" />
        </div>

        <?php
    }

    function insert_bottom_page() { ?>
        </fieldset>
        </form>
        </div>
        </body>
        </html>
        <?php
    }


