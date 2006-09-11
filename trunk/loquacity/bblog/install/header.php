<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="content-type" content="text/html"; charset="UTF-8" />
        <title>bBlog Installer</title>
        <link rel="stylesheet" type="text/css" title="Main" href="style/admin.css" media="screen" />
        <style type="text/css">
            #teaser a {
                cursor:default;
            }
            #teader a:hover {
            
            }
        </style>
    </head>
    <body>
        <div id="header">
            <h1>Loquacity Guided Installer</h1>
        </div>
        <div id="teaser">
            <a  class="active" href="Javascript:;">Welcome</a>
            <a  <?php if ($step>1) echo 'class="active" '; ?> href="Javascript:;">File Permissions</a>
            <a <?php if ($step >2) echo 'class="active" '; ?> href="Javascript:;">Database Settings</a>
            <a <?php if ($step >3) echo 'class="active" '; ?> href="Javascript:;">Config</a>
            <a <?php if ($step >4) echo 'class="active" '; ?> href="Javascript:;">Finish</a>
            <a href="install.php?reset=true" style="cursor:pointer;">Reset install, start over.</a>
        </div>
        <div id="content">
            <form action="install.php" method="POST" name="install">
