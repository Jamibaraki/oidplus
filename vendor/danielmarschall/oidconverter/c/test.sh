#!/bin/sh

make

# echo "-- test A"
# ./oid -x "06 07 01 80 80 80 80 80 7F"

# echo "-- test B"
# ./oid -x "06 02 80 01"

# echo "-- test C"
# ./oid -x "06 02 80 7F"

echo "-- 2.999";
./oid 2.999
echo "-- RELATIVE.2.999";
./oid RELATIVE.2.999

echo "-- 06 00"
./oid -x "06 00";
echo "-- 06 80"
./oid -x "06 80";
echo "-- 06 FF";
./oid -x "06 FF";

echo "-- 05 02 88 37"
./oid -x "05 02 88 37";

echo "-- 06 02 88 37"
./oid -x "06 02 88 37"

echo "-- 0D 03 02 87 67"
./oid -x "0D 03 02 87 67"

exit

echo "LONG OID"
./oid -x "06 82 02 4C 88 37 82 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 80 7f"

echo "-1.0 [NOT VALID]"
./oid -1.0
echo ""

echo "0.0"
./oid 0.0
echo ""

echo "0.39"
./oid 0.39
echo ""

echo "0.40 [NOT VALID]"
./oid 0.40
echo ""

echo "1.39"
./oid 1.39
echo ""

echo "1.40 [NOT VALID]"
./oid 1.40
echo ""

echo "3.0 [NOT VALID]"
./oid 3.0
echo ""

echo "2.999 == (06 02 88 37)";
./oid 2.999
echo ""

echo "(06 01 27)"
./oid -x "06 01 27"
echo "(06 01 4F)"
./oid -x "06 01 4F"
echo "(06 01 7F)"
./oid -x "06 01 7F"
echo "(06 01 80) [NOT VALID]"
./oid -x "06 01 80"
echo "(06 02 88 37)"
./oid -x "06 02 88 37"
