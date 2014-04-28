<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <title>
            Payplug - PHP library tests
        </title>
    </head>
    <body>
        <form action="payment.php" method="POST">
            <table>
                <tr>
                    <td>
                        <label for="email">Email</label>
                    </td>
                    <td>
                        <input id="email" name="email" type="text" />
                    </td>
                </tr>
                <tr>
                    <td>
                        <label for="firstName">First Name</label>
                    </td>
                    <td>
                        <input id="firstName" name="firstName" type="text" />
                    </td>
                </tr>
                <tr>
                    <td>
                        <label for="lastName">Last Name</label>
                    </td>
                    <td>
                        <input id="lastName" name="lastName" type="text" />
                    </td>
                </tr>
                <tr>
                    <td>
                        <label for="amount">Montant</label>
                    </td>
                    <td>
                        <input id="amount" name="amount" type="text" />
                    </td>
                </tr>
                <tr>
                    <td>
                        <label for="ipnUrl">IPN URL</label>
                    </td>
                    <td>
                        <input id="ipnUrl" name="ipnUrl" type="text" value="" />
                    </td>
                </tr>
            </table>
            <input type="submit" value="Go" />
        </form>
    </body>
</html>
