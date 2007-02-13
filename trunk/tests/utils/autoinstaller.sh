#!/bin/sh

# =================================================================
# bBlog Testing Framework
#
# Author: Kenneth Power <kenneth.power@gmail.com>
#
# Prepares a test environment for Loquacity and runs some rudimentary 
# checks.
#
#
# Feb 11, 2007:
#	Revived this script and converted to Loquacity usage
# =================================================================

#Setup environment
VERSION="0.0.7"
# baseDir must be fully qualified
BASE_DIR=
TEST_URI=
CUR_DIR=$PWD
TARBALL=
DB=
DB_USER=
DB_WORD=
SVN_USER=
MYSQL_USER=
ORIGIN=`pwd`

while [ $# -gt 0 ]; do
  case "$1" in
    --base-dir | -b ) BASE_DIR=$2 ; shift 2 ;;
    --dev-user | -d ) SVN_USER=$2 ; shift 2 ;;
    --file | -f ) TARBALL="$2" ; shift 2 ;;
    --mysql-user | -m ) MYSQL_USER=$2 ; shift 2 ;;
    -p ) DB_WORD=$2 ; shift 2 ;;
    --test-uri | -t ) TEST_URI=$2 ; shift 2 ;;
    -u ) DB_USER=$2 ; shift 2 ;;
    -ver* ) echo "Version $VERSION" ; exit 1 ;;
    * ) echo "Unknown argument" ; break ;;
  esac
done

if [[ $BASE_DIR == "" ]]; then
	echo "BASE_DIR undefined. Please edit this script and assign a value to BASE_DIR or specify the full path with the --base-dir parameter"
	exit 1
fi

if [[ $TEST_URI == "" ]]; then
	echo "TEST_URI undefined. Please edit this script and assign a value to BASE_DIR or specify the full path with the --test-uri parameter"
	exit 1
fi


echo 'Creating test folders...'
# If base directory does not exist, create it
if [[ ! -d $BASE_DIR ]]; then
    echo "Creating base directory for all tests...$BASEDIR"
    mkdir -p $BASE_DIR
fi

if [[ ! -d "$ORIGIN/runs" ]]; then
	mkdir -p "$ORIGIN/runs"
fi

#Move into base directory
cd $BASE_DIR

#Is there a record file in this directory? If not, create it
if [[ ! -f "record" ]]; then
	`touch record`
fi

#Open the record file
#What is the number in this file? This is the next test record
record=`cat record`

if [[ $record == "" ]]; then
  record=1
fi

#Does a directory exist for this test record?
if [[ ! -d $record ]]; then
    #If not, create it
    echo "Creating current test directory [$record] ..."
    `mkdir $record`
  else
    #If yes, we have an error. Stop and report it
    echo "Error. Record $record already exists. Not proceeding"
    exit 1
fi

if [[ ! -d "$ORIGIN/runs/$record" ]]; then
	mkdir -p "$ORIGIN/runs/$record"
fi

#Increment the record number by one
newrecord=$(( $record + 1 ))

#Store new record number in record file in today's date folder\record file
echo $(( $record + 1 )) > "record"

DB="$record"
if [[ $DB_USER == "" ]]; then
  DB_USER=$DB"user"
fi

if [[ $DB_WORD == "" ]]; then
  DB_WORD="temp1234"
fi

#Close record file
cd $record
#Placing Loquacity files into test folder:
#    Scenario 1:
#        Need location of Loquacity.tar.gz (could be an argument to script)
if [[ $TARBALL != "" ]] && [[ -f $TARBALL ]]; then
  # Untar Loquacity.tar.gz into test folder
  echo 'Unpacking Loquacity...'
  `tar xzvf $TARBALL 2>&1 > /dev/null`
#    Scenario 2:
#        Use subversion to download a copy
#        Need two different methods: developer and anonymous access
elif [[ $SVN_USER != "" ]]
  then
    # As a Loquacity developer
    echo "Exporting from Berlios as $SVN_USER..."
    /usr/bin/svn export --force svn+ssh://$SVN_USER@svn.berlios.de/svnroot/repos/loquacity/trunk/loquacity . 2>&1 > /dev/null
else
  # As anonymous
  echo "Exporting from Berlios Anonymously..."
  /usr/bin/svn export --force svn://svn.berlios.de/loquacity/trunk/loquacity . 2>&1 > /dev/null
fi


chmod -R 0777 core/generated/templates/ core/generated/cache/ core/generated/backup
chmod 0666 core/config.php core/generated/cache/favorites.xml


#Create test database
#Grant all privileges to test user
echo 'Creating database...'
if [[ $MYSQL_USER == "" ]]; then
  MYSQL_USER="root"
fi

echo 'Granting permissions to database user...'
echo "CREATE DATABASE \`$DB\`; GRANT ALL PRIVILEGES ON \`$DB\`.* TO $DB_USER@localhost IDENTIFIED BY '$DB_WORD'; FLUSH PRIVILEGES;" | mysql -u $MYSQL_USER -p

#Run tests
echo 'Setup complete.'
echo 'DB: '$DB
echo 'DB User: '$DB_USER
echo 'DB Password: '$DB_WORD

cd "$ORIGIN/runs/$record"
cat > "uninstall.sh" <<EOM
#!/bin/sh

CUR_DIR="$PWD"
echo "Removing this Loquacity test..."
echo "Removing Database and user..."
mysql -u $MYSQL_USER -p < mysqlscript
cd ..
echo "Removing test folder \$CUR_DIR..."
\`rm -rf \$CUR_DIR\`
echo "Uninstall complete..."
EOM

cat > "mysqlscript" << EOM
# This script is used by the uninstall process to remove the MySQL 
# specific portions of this setup

REVOKE ALL PRIVILEGES ON \`$DB\`.* FROM $DB_USER;
DELETE FROM mysql.user WHERE User='$DB_USER';
FLUSH PRIVILEGES;
DROP DATABASE \`$DB\`;
EOM

cat > "Loquacity_config.php" << EOM
<?php

define('blog_url', "$TEST_URI/$record/");
define('blogname', "blog_$DB");
define('blogdescription', "Testing installation $DB");
define('author_name', "Full Name");
define('login_name', "$DB_USER");
define('db_password', "$DB_WORD");
define('login_password', "$DB_WORD");
define('email_address', "me@example.com");
define('db_username', "$DB_USER");
define('db_password', "$DB_WORD");
define('db_database', "$DB");
define('db_host', "localhost");
?>
EOM

cd $ORIGIN

echo 'Performing a simple test run that does a basic install of Loquacity...'
#/usr/bin/php cli_test.php