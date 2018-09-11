<?php
/********************************************************
*
* Mpc Installer - Run from domain root directory
*
* Larry Sherman (c)2016
*
*/
if( isset($_POST['mpc-plugin-dir']) )
{
    // if wp plugin version
    if( is_file($_POST['mpc-plugin-dir'].'/mpc-installer-plugin/mpc-installer-plugin.php') ) unlink($_POST['mpc-plugin-dir'].'/mpc-installer-plugin/mpc-installer-plugin.php');
    if( is_dir($_POST['mpc-plugin-dir'].'/mpc-installer-plugin') ) rmdir($_POST['mpc-plugin-dir'].'/mpc-installer-plugin');
}
$docroot = realpath($_SERVER['DOCUMENT_ROOT']);
if(__DIR__ != $docroot) {
    // must be run from domain root
    header("location: https://masspagecreator.com/ck/errpage.php?cmd=docroot");
    exit();
}
$mfolder = (isset($_POST['mpcfolder']))? $_POST['mpcfolder'] : '';
$mfile = (isset($_POST['mpcfile']))? $_POST['mpcfile'] : '';
if( ($mfolder != '') && ($mfile != '') )
{
    // [Install] clicked
    $cbover = (isset($_POST['cbover']))? $_POST['cbover'] : 'no';
    if( (file_exists($mfolder)) && ($cbover != 'yes') ) {
        // no folder overwrite
        header("location: https://masspagecreator.com/ck/errpage.php?cmd=folder_ex&parm=$mfolder");
        exit();
    }
    // ok to install.. get the mpc binary
    $ch = curl_init('http://masspagecreator.com/ck/auto/mpc.bin');
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 0);
    $z = curl_exec($ch);
    curl_close($ch);
    if(! $z)
    {
        header("location: http://masspagecreator.com/ck/errpage.php?cmd=install_err");
        exit();
    }

    // write mpcfolder/file
    umask(022);
    mkdir($mfolder, 0777);
    file_put_contents("$mfolder/${mfile}.php", $z);
    //
    $UPL = "<?php \$V='0';\$U='advertiseinphilly215@gmail.com';\$P='d6ce261f960ff179240c92fd493430a0';\$L='f69a37a913c56947fcc1d40f3f962620';\n";
    // write login file (if embedded)
    if(strlen($UPL) > 3) file_put_contents("$mfolder/${mfile}_ini.php", $UPL);

    // remove this script and point browser to mpc.php file
    unlink(__FILE__);
    header("location: /$mfolder/${mfile}.php");
    exit();
}
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8" />
<title>MPC - Installer</title>
<link href="https://masspagecreator.com/ck/ckassets/support/support.css" rel="stylesheet" type="text/css" />
<link href="https://masspagecreator.com/ck/ckassets/sxy/sexy.css" rel="stylesheet" type="text/css" />
<style type="text/css">
input#mpcfolder, input#mpcfile {
    width:150px;
    text-align:right;
    padding:4px 2px;
    margin:0 2px 0 10px;
    font-family: monospace;
    font-size:1.2em;
    border:1px solid #808080;
    -moz-border-radius-topleft:5px;
    border-top-left-radius:5px;
    webkit-border-top-left-radius:5px;
}
.vpink {
    margin-left:20px;
    color:#c00000;
    font-weight:bold;
}
.busycss {
  background:none;
  border: 4px solid transparent;
  border-radius: 50%;
  border-top: 4px solid #4388c7;
  border-bottom: 4px solid #4386c7;
  width:16px;
  height: 16px;
  -webkit-animation: spin 1s linear infinite;
  -moz-animation: spin 1s linear infinite;
  -ms-animation: spin 1s linear infinite;
  animation: spin 1s linear infinite;
}

@-webkit-keyframes spin {
  0% { -webkit-transform: rotate(0deg); }
  100% { -webkit-transform: rotate(360deg); }
}

@-moz-keyframes spin {
  0% { -moz-transform: rotate(0deg); }
  100% { -moz-transform: rotate(360deg); }
}

@-ms-keyframes spin {
  0% { -ms-transform: rotate(0deg); }
  100% { -ms-transform: rotate(360deg); }
}

@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}
</style>
<script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js" ></script>
<script>
function vform()
{
    var jfolder = jQuery('#mpcfolder');
    var jfile = jQuery('#mpcfile');
    var jstfolder = jQuery('#stfolder');
    var jstfile = jQuery('#stfile');
    var mfolder = '';
    var mfile = '';

    if(jfolder.val() == '') mfolder = 'Folder name required';
    else if(jfolder.val() == 'mpc') mfolder = 'Cannot name folder "mpc"';

    if(jfile.val() == '') mfile = 'File name required';
    else if(jfile.val() == 'mpc') mfile = 'Cannot name file "mpc.php"';

    if( (jfolder.val() == jfile.val()) && (jfile.val() != '') ) mfile = 'For privacy, folder and file must differ';

    if( (mfolder != '') || (mfile != '') )
    {
        if(mfolder != '')
        {
            jfolder.css('background-color', 'pink');
            jstfolder.html(mfolder);
            jstfolder.css('visibility', 'visible');
            jfolder.focus();
        }

        if(mfile != '')
        {
            jfile.css('background-color', 'pink');
            jstfile.html(mfile);
            jstfile.css('visibility', 'visible');
            jfile.focus();
        }
        return false;
    }

    jQuery('#bnInstall').prop('disabled', true);
    jQuery('#ttlbusy').toggleClass('busycss', true);
    return true;
}

function unpink()
{
    jQuery('.epink').css('background-color', 'white');
    jQuery('.vpink').css('visibility', 'hidden');
}
</script>

<script>
jQuery(document).ready(function() {
   $(function() {
      $('.epink').keyup(function(e) {
            var kcode = e.keyCode ? e.keyCode : e.which;
            if( (kcode == 8) || (kcode == 37) || (kcode == 39) ) return;
            if(this.value.match(/[^a-z0-9]/g))
            {
                this.value = this.value.toLowerCase();
                this.value = this.value.replace(/[^a-z0-9]/g, '');
            }
      });
   });
});
</script>

</head>
<body>
<div id="h100" spellcheck=false>
    <div style="height:8px"></div> 
    <div class="mpcnav">
        <a class="mpclogo" href="http://seohelpnow.com"><img class="img" src="http://seohelpnow.com/img/logo.png" alt="" /></a>
        <div style="clear:both"></div>
    </div>

    <div id="support-wrap">
        <h1>SEO Help Now Installer - ATTENTION CLIENTS DO NOT FILL OUT!</h1>
        <div style="border-top:1px solid #ddd;line-height:22px;margin:0;padding:10px 0 0">
            <div style="font-size:14px;line-height:normal;" onmousedown="unpink();return true;">
  <p>
</p>
<div style="position:relative">
<form action="" method="post" onsubmit="return vform()">
<table border="0" cellpadding="0" cellspacing="0">
    <tr>
        <td style="width:80px;white-space:nowrap"><b>Folder</b></td>
        <td colspan="2" style="width:150px">
            <input id="mpcfolder" class="epink" name="mpcfolder" type="text" onkeyup="unpink()" title="a-z and 0-9 only" />
        </td>
        <td><b>/</b></td>
        <td><div id="stfolder" class="vpink" style="visibility:hidden">Folder name required and cannot be "m"</div></td>
    </tr>

    <tr><td colspan="5"><div style="height:10px"></div></td></tr>

    <tr>
        <td style="text-align:right;width:80px;white-space:nowrap"><b> File</b></td>
        <td colspan="2" style="width:150px">
            <input id="mpcfile" class="epink" name="mpcfile" type="text" onkeyup="unpink()" title="a-z and 0-9 only" />
        </td>
        <td><b>.php</b></td>
        <td><div id="stfile" class="vpink" style="visibility:hidden">File name required and cannot be "m"</div></td>
    </tr>

    <tr><td colspan="5"><div style="height:61px"></div></td></tr>
</table>

<div style="position:absolute;bottom:17px;left:0"><button id="bnInstall" type="submit" class="sexybutton"><span><span><span class="accept">Install</span></span></span></button></div>
<div id="ttlbusy" class="" style="position:absolute;bottom:17px;left:96px"></div>

<!--
<table border="0" cellpadding="0" cellspacing="0">
    <tr>
        <td style="padding:0 5px 0 0;width:5px;white-space:nowrap">
            <input type="checkbox" name="cbover" value="yes" id="cbover">
        </td>
        <td style="text-align:right;width:80px;white-space:nowrap">
            <label for="cbover">Ok to Overwrite file and folder</label>
        </td>
    </tr>
</table>
-->
</form>
</div>
<hr>


            <div style="clear:both;font-size:.8em;text-align:center;padding-top:5px"></div>

            </div>
        </div>
    </div>
            <div style="font-size:.8em;text-align:center;padding-top:5px">
                Copyright &copy;2011-2017  Sherman&nbsp;&nbsp;All Rights Reserved
            </div>
    <br>
</div>
</body>
</html>
