<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
<!--
     @(#) $Id: xml_schema.xsl,v 1.1 2002-09-04 14:47:04 dickmann Exp $
  -->
<xsl:template match="/">
    <!--  -->
    <br/><span class="titlemini">database
    <xsl:value-of select="/database/name"/></span><br/><br/>
    <TABLE class="tablemain" width="400">
    <xsl:for-each select="/database/table">
        <xsl:call-template name="showtable"/>
    </xsl:for-each>
    </TABLE>
    <!-- -->
    <!-- -->
</xsl:template>

<xsl:template name="showtable">
    <tr><td colspan="4" class="tableheader"> <span class="titlemini">table
        <xsl:value-of select="name"/></span> </td></tr>
    <tr><td colspan="4"><span class="textmini">
        <xsl:value-of select="comment"/></span></td></tr>

    <tr><td class="tableheader">field</td>
    <td class="tableheader">type</td>
    <td class="tableheader">comment</td>
    <td class="tableheader">attributes</td>
    </tr>

    <xsl:for-each select="descendant::declaration/field">
        <xsl:call-template name="showfield"/>
    </xsl:for-each>

    <tr><td colspan="2" height="18">  </td> </tr>
</xsl:template>

<xsl:template name="showfield">
    <tr><td class="tablebody"><span class="textmini">
    <xsl:value-of select="name"/></span></td>
    <td class="tablebody"><span class="textmini">
    <xsl:value-of select="type"/>
    <xsl:for-each select="descendant::length"><xsl:call-template name="showlength"/>
    </xsl:for-each>
    </span></td>

    <td class="tablebody"><span class="textmini">
    <xsl:value-of select="comment"/></span></td>

    <td class="tablebody"><span class="textmini">
    <xsl:for-each select="descendant::default"><xsl:call-template name="showdefault"/>
    </xsl:for-each>

    <xsl:for-each select="descendant::notnull"><xsl:call-template name="shownotnull"/>
    </xsl:for-each>

    <xsl:variable name="curfield" select="current()/name"/>

    <xsl:for-each select="following-sibling::index">
         <xsl:if test="$curfield=field/name">
         <br/>index <xsl:value-of select="name"/>
            <xsl:if test="unique"> unique</xsl:if>
         </xsl:if>

    </xsl:for-each>

    </span></td>

    </tr>
</xsl:template>

<xsl:template name="showlength">(<xsl:value-of select="//length"/>)
</xsl:template>

<xsl:template name="showdefault">
    default=<xsl:value-of select="//default"/>
</xsl:template>

<xsl:template name="shownotnull">
    notnull=<xsl:value-of select="//notnull"/>
</xsl:template>

</xsl:stylesheet>
