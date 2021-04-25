#!/bin/bash

# We don't want to leave these files in an area accessible from the
# web - some unauthorized person may get ahold of them

outpath=/mnt/data/public/carving

# make sure the timestamp matches in all files by only generating it once
timestamp=$(date +%F)
db_filename="carving-db-$timestamp.sql"
tar_filename="carving-db-$timestamp.tar.bz2"
zip_filename="carving-db-$timestamp.zip"

# dump the database structure and data
pg_dump -U postgres carving > $db_filename
if [ $? != 0 ]; then
	echo "Dump failed"
	exit
fi

# create archive including the database and associated pictures
tar cjf $tar_filename $db_filename pictures
mv $tar_filename $outpath

# create zip for Windows users
zip $zip_filename -q -r -b . $db_filename pictures
chmod 644 $zip_filename
mv $zip_filename $outpath

# remove the database dump once it is archived
rm $db_filename

echo $outpath
