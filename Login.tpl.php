<HTML>
<HEAD>
  <TITLE>MDB Manager</TITLE>
</HEAD>
<BODY bgcolor="#f5f5f5">
  <FORM action="Frontend.php" method="post">
  <TABLE border="0">
  <TR>
    <TD colspan="2">
      <?php if (isset($error)) {?>
        <font color="#ff0000"><?php echo nl2br($error); ?></font>
      <?php }; ?>
    </TD>
  </TR>
  <TR>
    <TD>
      <b>Hostname:</b>
    </TD>
    <TD>
      <input type="text" name="formData[hostname]" value="<?php echo $formData['hostname']; ?>">
    </TD>
  </TR>
  <TR>
    <TD>
      <b>Username:</b>
    </TD>
    <TD>
      <input type="text" name="formData[username]" value="<?php echo $formData['username']; ?>">
    </TD>
  </TR>
  <TR>
    <TD>
      <b>Password:</b>
    </TD>
    <TD>
      <input type="password" name="formData[password]" value="<?php echo $formData['password']; ?>">
    </TD>
  </TR>
  <TR>
    <TD>
      <b>Database Type:</b>
    </TD>
    <TD>
      <input type="text" name="formData[dbtype]" value="<?php echo $formData['dbtype']; ?>">
    </TD>
  </TR>
  <TR>
    <TD>
      <input type="submit" value="Connect to Database">
    </TD>
  </TR>
  </TABLE>
  </FORM>
</BODY>
</HTML>