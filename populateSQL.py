#!/usr/bin/env python
import MySQLdb
from datetime import datetime
from os import system
from numpy import array

system("rm show30")
system("wget http://maps.azstarnet.com/crime/show30 > /dev/null")
system("/home/tjarnold/public_html/GMaps/extract show30 > tmp")

lat  = []
lng  = []
date = []
crime= []
UID  = []

# Convert space delimited columns into arrays
f = open("tmp")
eof = False
while (eof == False):
    line = f.readline()
    if line == "":
        eof = True
    else:
        line = line.strip("\n").split(" ")
        spacesRemoved = False
        while spacesRemoved == False:
            try: line.remove('')
            except ValueError: spacesRemoved = True
        if len(line) == 5:
            lat.append(float(line[0]))
            lng.append(float(line[1]))
            crime.append(line[3])
            UID.append(int(line[4]))

            # Rearrange the date from m/dd/yyyy to yyyy-mm-dd so that MySQL is happy
            dateArr = line[2].split("/")
            date.append("%04s-%02s-%02s" % (dateArr[2], dateArr[0], dateArr[1]))

# Convert these to numpy arrays
lat  = array(lat)
lng  = array(lng)
date = array(date)
crime= array(crime)
UID  = array(UID)

print lat, lng, date, crime, UID

system("rm show30")
system("rm tmp")

# Open a connection to MySQL
db = MySQLdb.connect(\
        host="localhost",\
        user="GoogleHeatMap",\
        passwd="7ynMELdb",\
        db="GoogleHeatMap")
c = db.cursor()

rowsAdded = 0
for i in range(len(lat)):
    c.execute ("""
         SELECT UniqueID FROM TucsonCrime WHERE UniqueID=%s""", (str(UID[i])))
    # If the row doesn't yet exist in the MySQL table...
    if c.fetchone() == None:
        c.execute ("""
             INSERT INTO TucsonCrime 
             (Latitude, Longitude, EventDate, UniqueID, CrimeType)
             VALUES (%s, %s, %s, %s, %s)""",\
                (str(lat[i]), str(lng[i]), date[i], str(UID[i]), crime[i]))
        rowsAdded += 1

c.close ()
db.close ()
print "%4.0f rows added" % (rowsAdded)
