<?xml version="1.0" encoding="UTF-8"?>

<phpunit
    backupGlobals               = "false"
    backupStaticAttributes      = "false"
    colors                      = "true"
    convertErrorsToExceptions   = "true"
    convertNoticesToExceptions  = "true"
    convertWarningsToExceptions = "true"
    processIsolation            = "false"
    stopOnFailure               = "false"
    syntaxCheck                 = "false"
	bootstrap					= "./app/modules/application/src/Tests/bootstrap.php">

    <testsuites>
        <testsuite name="Pagekit Tests">
    		<directory>./app/modules/*/src/</directory>
            <directory>./app/system/modules/*/src/</directory>
        </testsuite>
    </testsuites>

    <filter>
        <whitelist>
            <exclude>
                <directory>./app/vendor/</directory>
                <directory>./*/src/*/Tests/</directory>
                <directory>./*/src/Tests</directory>
            </exclude>
        </whitelist>
    </filter>

    <php>
        <!-- "Real" test database -->
        <!-- uncomment, otherwise sqlite memory runs -->
        <!-- <var name="db_type" value="pdo_mysql"/>
        <var name="db_host" value="localhost" />
        <var name="db_username" value="root" />
        <var name="db_password" value="" />
        <var name="db_name" value="doctrine_tests" />
        <var name="db_port" value="3306"/>  -->
        <!-- <var name="db_event_subscribers" value="Doctrine\DBAL\Event\Listeners\OracleSessionInit"> -->

        <!-- Database for temporary connections (i.e. to drop/create the main database) -->
        <var name="tmpdb_type" value="pdo_mysql"/>
        <var name="tmpdb_host" value="localhost" />
        <var name="tmpdb_username" value="root" />
        <var name="tmpdb_password" value="" />
        <var name="tmpdb_name" value="doctrine_tests_tmp" />
        <var name="tmpdb_port" value="3306"/>

        <!-- uncomment, otherwise email won't be tested-->
        <var name="email_adress" value=""/>
        <var name="email_to" value=""/>
        <var name="email_smtp_host" value=""/>
        <var name="email_smtp_port" value=""/>
        <var name="email_smtp_encryption" value=""/>
        <var name="email_smtp_user" value=""/>
        <var name="email_smtp_password" value=""/>

        <!-- uncomment, otherwise ftp won't be tested-->
        <var name="ftp_host" value="localhost"/>
        <var name="ftp_port" value="21"/>
        <var name="ftp_user" value="anonymous"/>
        <var name="ftp_pass" value=""/>
        <var name="ftp_passive" value="false"/>
        <var name="ftp_mode" value="FTP_ASCII"/> <!-- FTP_ASCII or FTP_BINARY-->
    </php>

</phpunit>
