

<?php
$getprt=printer_list(PRINTER_ENUM_LOCAL);
$printers = serialize($getprt);
$printers= unserialize($printers);
//print_r($printers);
echo '<select name="printers">';
foreach ($printers as $PrintDest)
echo "<option value=".$PrintDest["NAME"].">".explode(",",$PrintDest["DESCRIPTION"])[1]."</option>";
echo '</select>';
?>





<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <link rel="stylesheet" href="style.css">
        <title>Receipt example</title>
        <style>
            * {
    font-size: 12px;
    font-family: 'Times New Roman';
}

td,
th,
tr,
table {
    border-top: 1px solid black;
    border-collapse: collapse;
}

td.description,
th.description {
    width: 75px;
    max-width: 75px;
}

td.quantity,
th.quantity {
    width: 40px;
    max-width: 40px;
    word-break: break-all;
}

td.price,
th.price {
    width: 40px;
    max-width: 40px;
    word-break: break-all;
}

.centered {
    text-align: center;
    align-content: center;
}

.ticket {
    width: 155px;
    max-width: 155px;
}

img {
    max-width: inherit;
    width: inherit;
}

@media print {
    .hidden-print,
    .hidden-print * {
        display: none !important;
    }

   html, body {
    width: 80mm;
    height:100%;
    position:absolute;
   }
}
        </style>
    </head>
    <body>
        <div class="ticket">
            <img src="tux.png" alt="Logo">
            <p class="centered">RECEIPT EXAMPLE
                <br>Address line 1
                <br>Address line 2</p>
            <table>
                <thead>
                    <tr>
                        <th class="quantity">Q.</th>
                        <th class="description">Description</th>
                        <th class="price">$$</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="quantity">1.00</td>
                        <td class="description">ARDUINO UNO R3</td>
                        <td class="price">$25.00</td>
                    </tr>
                    <tr>
                        <td class="quantity">2.00</td>
                        <td class="description">JAVASCRIPT BOOK</td>
                        <td class="price">$10.00</td>
                    </tr>
                    <tr>
                        <td class="quantity">1.00</td>
                        <td class="description">STICKER PACK</td>
                        <td class="price">$10.00</td>
                    </tr>

                </tbody>
            </table>
            <p class="centered">Thanks for your purchase!
                <br>parzibyte.me/blogt]]g</p>
        </div>
        <button id="btnPrint" class="hidden-print">Print</button>
        <script src="script.js">
        const $btnPrint = document.querySelector("#btnPrint");
$btnPrint.addEventListener("click", () => {
    window.print();
    
    
    var pp = this.getPrintParams();
    pp.interactive = pp.constants.interactionLevel.automatic;
    pp.printerName = "Adobe PDF";
    this.print(pp);
});
        </script>
    </body>
</html>