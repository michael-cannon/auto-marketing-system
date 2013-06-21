#!/bin/sh

phpdoc -p -t ../docs/apidoc -d "../PayPal/SDK,../PayPal/Profile,../PayPal/Type" \
    -f "../PayPal/Error.php,../PayPal/CallerServices.php,../PayPal/EWPServices.php,../PayPal/Profile.php,../PayPal/SDK.php,../PayPal.php" \
    -po Services_PayPal -ti 'PayPal SDK API Documentation' -dn Services_PayPal
