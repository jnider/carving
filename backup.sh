#!/bin/bash

filename="carving-db-$(date +%F).sql"

echo $filename
pg_dump -U postgres carving > ../$filename
