
<table cellpadding="0" cellspacing="0" border="0" align="left" width="448" id="c448p80r"  style="float:left" class="c448p80r">
  <tr>
    <td valign="top"  style="padding:0px">
      <table cellpadding="0" cellspacing="0" border="0" width="100%">
        <tr>
          <td valign="top"  style="padding:10px">
            <table cellpadding="0" cellspacing="0" width="100%">
              <tr>
                <td  style="padding:0px">
                  <table cellpadding="0" cellspacing="0" border="0" width="100%"  style="border-top:2px solid #ffca00">
                    <tr>
                      <td valign="top">
                        <table cellpadding="0" cellspacing="0" width="100%">
                          <tr>
                            <td  style="padding:0px"></td>
                          </tr>
                        </table>
                      </td>
                    </tr>
                  </table>
                </td>
              </tr>
            </table>
          </td>
        </tr>
      </table>
      <table cellpadding="0" cellspacing="0" border="0" width="100%">
        <tr>
          <td valign="top"  style="padding:10px">
            <div  style="text-align:left;font-family:Verdana, Geneva, sans-serif;font-size:16px;color:#000000;line-height:24px;mso-line-height:exactly;mso-text-raise:4px">
              <?php
              foreach ( $email_parts as $part ) {
                include portal_template_hierarchy( "/email/$part.php" );
              }
              ?>
            </div>
          </td>
        </tr>
      </table>

    </td>
  </tr>
</table>
