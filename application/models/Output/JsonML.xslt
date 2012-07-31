<?xml version="1.0" encoding="utf-8" ?>
<!--
		JsonML.xslt

		Created: 2006-11-15-0551
		Modified: 2009-02-14-0927

		Released under an open-source license:
		http://jsonml.org/License.htm

		This transformation converts any XML document into JsonML.
		It omits processing-instructions and comment-nodes.
		
		To enable comment-nodes to be emitted as JavaScript comments,
		uncomment the Comment() template.
-->
<xsl:stylesheet version="1.0"
				xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

	<xsl:output method="text"
				media-type="application/json"
				encoding="UTF-8"
				indent="no"
				omit-xml-declaration="yes" />

	<!-- constants -->
	<xsl:variable name="XHTML"
				  select="'http://www.w3.org/1999/xhtml'" />

	<xsl:variable name="START_ELEM"
				  select="'['" />

	<xsl:variable name="END_ELEM"
				  select="']'" />

	<xsl:variable name="VALUE_DELIM"
				  select="','" />

	<xsl:variable name="START_ATTRIB"
				  select="'{'" />

	<xsl:variable name="END_ATTRIB"
				  select="'}'" />

	<xsl:variable name="NAME_DELIM"
				  select="':'" />

	<xsl:variable name="STRING_DELIM"
				  select="'&quot;'" />

	<xsl:variable name="START_COMMENT"
				  select="'/*'" />

	<xsl:variable name="END_COMMENT"
				  select="'*/'" />

	<!-- root-node -->
	<xsl:template match="/">
		<xsl:apply-templates select="*" />
	</xsl:template>

	<!-- comments -->
	<xsl:template match="comment()">
	<!-- uncomment to support JSON comments -->
	<!--
		<xsl:value-of select="$START_COMMENT" />

		<xsl:value-of select="."
					  disable-output-escaping="yes" />

		<xsl:value-of select="$END_COMMENT" />
	-->
	</xsl:template>

	<!-- elements -->
	<xsl:template match="*">
		<xsl:value-of select="$START_ELEM" />

		<!-- tag-name string -->
		<xsl:value-of select="$STRING_DELIM" />
		<xsl:choose>
			<xsl:when test="namespace-uri()=$XHTML">
				<xsl:value-of select="local-name()" />
			</xsl:when>
			<xsl:otherwise>
				<xsl:value-of select="name()" />
			</xsl:otherwise>
		</xsl:choose>
		<xsl:value-of select="$STRING_DELIM" />

		<!-- attribute object -->
		<xsl:if test="count(@*)>0">
			<xsl:value-of select="$VALUE_DELIM" />
			<xsl:value-of select="$START_ATTRIB" />
			<xsl:for-each select="@*">
				<xsl:if test="position()>1">
					<xsl:value-of select="$VALUE_DELIM" />
				</xsl:if>
				<xsl:apply-templates select="." />
			</xsl:for-each>
			<xsl:value-of select="$END_ATTRIB" />
		</xsl:if>

		<!-- child elements and text-nodes -->
		<xsl:for-each select="*|text()">
			<xsl:value-of select="$VALUE_DELIM" />
			<xsl:apply-templates select="." />
		</xsl:for-each>

		<xsl:value-of select="$END_ELEM" />
	</xsl:template>

	<!-- text-nodes -->
	<xsl:template match="text()">
		<xsl:call-template name="escape-string">
			<xsl:with-param name="value"
							select="." />
		</xsl:call-template>
	</xsl:template>

	<!-- attributes -->
	<xsl:template match="@*">
		<xsl:value-of select="$STRING_DELIM" />
		<xsl:choose>
			<xsl:when test="namespace-uri()=$XHTML">
				<xsl:value-of select="local-name()" />
			</xsl:when>
			<xsl:otherwise>
				<xsl:value-of select="name()" />
			</xsl:otherwise>
		</xsl:choose>
		<xsl:value-of select="$STRING_DELIM" />

		<xsl:value-of select="$NAME_DELIM" />

		<xsl:call-template name="escape-string">
			<xsl:with-param name="value"
							select="." />
		</xsl:call-template>

	</xsl:template>

	<!-- escape-string: quotes and escapes -->
	<xsl:template name="escape-string">
		<xsl:param name="value" />

		<xsl:value-of select="$STRING_DELIM" />

		<xsl:if test="string-length($value)>0">
			<xsl:variable name="escaped-whacks">
				<!-- escape backslashes -->
				<xsl:call-template name="string-replace">
					<xsl:with-param name="value"
									select="$value" />
					<xsl:with-param name="find"
									select="'\'" />
					<xsl:with-param name="replace"
									select="'\\'" />
				</xsl:call-template>
			</xsl:variable>

			<xsl:variable name="escaped-LF">
				<!-- escape line feeds -->
				<xsl:call-template name="string-replace">
					<xsl:with-param name="value"
									select="$escaped-whacks" />
					<xsl:with-param name="find"
									select="'&#x0A;'" />
					<xsl:with-param name="replace"
									select="'\n'" />
				</xsl:call-template>
			</xsl:variable>

			<xsl:variable name="escaped-CR">
				<!-- escape carriage returns -->
				<xsl:call-template name="string-replace">
					<xsl:with-param name="value"
									select="$escaped-LF" />
					<xsl:with-param name="find"
									select="'&#x0D;'" />
					<xsl:with-param name="replace"
									select="'\r'" />
				</xsl:call-template>
			</xsl:variable>

			<xsl:variable name="escaped-tabs">
				<!-- escape tabs -->
				<xsl:call-template name="string-replace">
					<xsl:with-param name="value"
									select="$escaped-CR" />
					<xsl:with-param name="find"
									select="'&#x09;'" />
					<xsl:with-param name="replace"
									select="'\t'" />
				</xsl:call-template>
			</xsl:variable>

			<!-- escape quotes -->
			<xsl:call-template name="string-replace">
				<xsl:with-param name="value"
								select="$escaped-tabs" />
				<xsl:with-param name="find"
								select="'&quot;'" />
				<xsl:with-param name="replace"
								select="'\&quot;'" />
			</xsl:call-template>
		</xsl:if>

		<xsl:value-of select="$STRING_DELIM" />
	</xsl:template>

	<!-- string-replace: replaces occurances of one string with another -->
	<xsl:template name="string-replace">
		<xsl:param name="value" />
		<xsl:param name="find" />
		<xsl:param name="replace" />

		<xsl:choose>
			<xsl:when test="contains($value,$find)">
				<!-- replace and call recursively on next -->
				<xsl:value-of select="substring-before($value,$find)"
							  disable-output-escaping="yes" />
				<xsl:value-of select="$replace"
							  disable-output-escaping="yes" />
				<xsl:call-template name="string-replace">
					<xsl:with-param name="value"
									select="substring-after($value,$find)" />
					<xsl:with-param name="find"
									select="$find" />
					<xsl:with-param name="replace"
									select="$replace" />
				</xsl:call-template>
			</xsl:when>
			<xsl:otherwise>
				<!-- no replacement necessary -->
				<xsl:value-of select="$value"
							  disable-output-escaping="yes" />
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>

</xsl:stylesheet>
