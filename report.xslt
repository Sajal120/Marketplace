<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
<!-- 
        Done By: Sajal Basnet
        Student Id: 104170062

        This XSLT document is designed to transform XML data from an auction system into a readable HTML format. 
        It generates an auction report detailing items that were either sold or failed. It also calculates the 
        total number of sold and failed items, as well as the total revenue from sold items.
    -->
     <!-- Set the output format to HTML and enable indentation for readability -->
    <xsl:output method="html" indent="yes"/>

     <!-- Root template matching 'auctions' element -->
    <xsl:template match="/auctions">
        <html>
        <body>
            <h2>Auction Report</h2>
            <table border="1">
                <tr>
                    <th>Item Number</th>
                    <th>Item Name</th>
                    <th>Category</th>
                    <th>Status</th>
                    <th>Start Price</th>
                    <th>Reserve Price</th>
                    <th>Buy It Now Price</th>
                    <th>Start Date</th>
                    <th>Start Time</th>
                </tr>
                 <!-- Apply templates to each item with either 'sold' or 'failed' status -->
                <xsl:apply-templates select="item[status = 'sold' or status = 'failed']"/>
            </table>

            <p>Total Sold Items: <xsl:value-of select="count(item[status = 'sold'])"/></p>
            <p>Total Failed Items: <xsl:value-of select="count(item[status = 'failed'])"/></p>
            <p>Total Revenue: 
                <xsl:call-template name="calculate-revenue">
                    <xsl:with-param name="items" select="item[status = 'sold']"/>
                </xsl:call-template>
            </p>
        </body>
        </html>
    </xsl:template>
    <!-- Template for transforming each 'item' element into a table row -->
    <xsl:template match="item">
        <tr>
            <td><xsl:value-of select="itemNumber"/></td>
            <td><xsl:value-of select="itemName"/></td>
            <td><xsl:value-of select="category"/></td>
            <td><xsl:value-of select="status"/></td>
            <td><xsl:value-of select="startPrice"/></td>
            <td><xsl:value-of select="reservePrice"/></td>
            <td><xsl:value-of select="buyItNowPrice"/></td>
            <td><xsl:value-of select="startDate"/></td>
            <td><xsl:value-of select="startTime"/></td>
        </tr>
    </xsl:template>

<!-- Recursive template for calculating the total revenue from sold items -->
    <xsl:template name="calculate-revenue">
        <xsl:param name="items"/>
        <xsl:param name="total" select="0"/>

        <xsl:choose>
            <xsl:when test="$items">
                <!-- Determine the startPrice value -->
                <xsl:variable name="startPrice" select="$items[1]/startPrice"/>
                <xsl:variable name="numericStartPrice" select="number($startPrice)"/>
                <!-- Check if the startPrice is a number -->
                <xsl:choose>
                    <xsl:when test="$numericStartPrice = $numericStartPrice">
                        <xsl:call-template name="calculate-revenue">
                            <xsl:with-param name="items" select="$items[position() > 1]"/>
                            <xsl:with-param name="total" select="$total + $numericStartPrice"/>
                        </xsl:call-template>
                    </xsl:when>
                    <xsl:otherwise>
                        <xsl:call-template name="calculate-revenue">
                            <xsl:with-param name="items" select="$items[position() > 1]"/>
                            <xsl:with-param name="total" select="$total"/>
                        </xsl:call-template>
                    </xsl:otherwise>
                </xsl:choose>
            </xsl:when>
            <xsl:otherwise>
                <!-- Output the total when no more items to process -->
                <xsl:value-of select="$total * 0.03"/>
            </xsl:otherwise>
        </xsl:choose>
    </xsl:template>

</xsl:stylesheet>
