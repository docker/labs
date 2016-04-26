<%@ Page Title="Home Page" Language="C#" MasterPageFile="~/Site.Master" AutoEventWireup="true" CodeBehind="Default.aspx.cs" Inherits="WebAppForm._Default" %>

<asp:Content ID="BodyContent" ContentPlaceHolderID="MainContent" runat="server">

    <div class="jumbotron">
        <h1>
        <asp:Label ID="errorCtl" runat="server" ForeColor="Red" Text="ErrorText"></asp:Label>
        </h1>
        <asp:Table ID="Table0" width="100%" runat="server">
                <asp:TableHeaderRow>
                    <asp:TableHeaderCell Width="20%"></asp:TableHeaderCell>
                    <asp:TableHeaderCell Width="20%">wunderground station</asp:TableHeaderCell>
                    <asp:TableHeaderCell Width="39%"></asp:TableHeaderCell>
                    <asp:TableHeaderCell Width="1%"></asp:TableHeaderCell>
                    <asp:TableHeaderCell Width="20%"></asp:TableHeaderCell>
                </asp:TableHeaderRow>
                <asp:TableRow runat="server">
                    <asp:TableCell Width="20%">Temp. Station:</asp:TableCell>
                    <asp:TableCell Width="20%"><asp:TextBox ID="tempStationCtl" runat="server">KILCHICA314</asp:TextBox></asp:TableCell>
                    <asp:TableCell Width="39%"><asp:Label ID="tempStationLocCtl"   runat="server" Text="temp station loc"></asp:Label></asp:TableCell>
                    <asp:TableCell Width="1%"></asp:TableCell>
                    <asp:TableCell Width="20%"><asp:Button ID="ctrlRefresh" runat="server" Text="Refresh" OnClick="ctrlRefresh_Click" /></asp:TableCell>
               </asp:TableRow>
                <asp:TableRow runat="server">
                    <asp:TableCell Width="20%">Wind Station:</asp:TableCell>
                    <asp:TableCell Width="20%"><asp:TextBox ID="windStationCtl" runat="server">KILCHICA30</asp:TextBox></asp:TableCell>
                    <asp:TableCell Width="39%"><asp:Label ID="windStationLocCtl" runat="server" Text="wind station loc"></asp:Label></asp:TableCell>
                    <asp:TableCell Width="1%"></asp:TableCell>
                    <asp:TableCell Width="20%"><asp:Label ID="asOfCtl" runat="server" Text="as of Mar 27, 3pm"></asp:Label></asp:TableCell>
               </asp:TableRow>
        </asp:Table>

            <asp:Table ID="Table1" width="100%" runat="server">
                <asp:TableHeaderRow>
                    <asp:TableHeaderCell Width="20%"></asp:TableHeaderCell>
                    <asp:TableHeaderCell Width="20%"></asp:TableHeaderCell>
                    <asp:TableHeaderCell Width="20%">to midpoint</asp:TableHeaderCell>
                    <asp:TableHeaderCell Width="20%"></asp:TableHeaderCell>
                    <asp:TableHeaderCell Width="20%">from midpoint</asp:TableHeaderCell>
                </asp:TableHeaderRow>

                <asp:TableRow runat="server">
                    <asp:TableCell Width="20%"></asp:TableCell>
                    <asp:TableCell Width="20%">
                    </asp:TableCell>
                    <asp:TableCell Width="20%">
                        <asp:TextBox ID="toMidpointCtl" runat="server" >07:00</asp:TextBox>
                    </asp:TableCell>
                    <asp:TableCell Width="20%">
                    </asp:TableCell>
                    <asp:TableCell Width="20%">
                        <asp:TextBox ID="fromMidpointCtl" runat="server" >17:30</asp:TextBox>
                    </asp:TableCell>
                </asp:TableRow>
            </asp:Table>
    </div>

        <div class="jumbotron">
            <asp:Table ID="Table3" width="100%" runat="server">
                <asp:TableHeaderRow>
                    <asp:TableHeaderCell Width="20%"><i>**TODAY</i></asp:TableHeaderCell>
                    <asp:TableHeaderCell Width="20%">NOW</asp:TableHeaderCell>
                    <asp:TableHeaderCell Width="20%">TO commute</asp:TableHeaderCell>
                    <asp:TableHeaderCell Width="20%"></asp:TableHeaderCell>
                    <asp:TableHeaderCell Width="20%">FROM commute</asp:TableHeaderCell>
                </asp:TableHeaderRow>
            
                <asp:TableRow runat="server">
                    <asp:TableCell Width="20%">
                        Temp. F:
                    </asp:TableCell>
                    <asp:TableCell Width="20%">
                        <asp:Label ID="beforeToTempCtl" runat="server" Text="22"></asp:Label>
                    </asp:TableCell>
                    <asp:TableCell Width="20%">
                        <asp:Label ID="toTempCtl" runat="server" Text="23"></asp:Label>
                    </asp:TableCell>
                    <asp:TableCell Width="20%">
                        <asp:Label ID="beforeFromTempCtl" runat="server" Text="32"></asp:Label>
                    </asp:TableCell>
                    <asp:TableCell Width="20%">
                        <asp:Label ID="fromTempCtl" runat="server" Text="33"></asp:Label>
                    </asp:TableCell>
                </asp:TableRow>
                <asp:TableRow runat="server">
                    <asp:TableCell Width="20%">
                        Wind mph:
                    </asp:TableCell>
                    <asp:TableCell Width="20%">
                        <asp:Label ID="beforeToWindCtl" runat="server" Text="12 NNE+2"></asp:Label>
                    </asp:TableCell>
                    <asp:TableCell Width="20%">
                        <asp:Label ID="toWindCtl" runat="server" Text="11 N"></asp:Label>
                    </asp:TableCell>
                    <asp:TableCell Width="20%">
                        <asp:Label ID="beforeFromWindCtl" runat="server" Text="9 SW+10"></asp:Label>
                    </asp:TableCell>
                    <asp:TableCell Width="20%">
                        <asp:Label ID="fromWindCtl" runat="server" Text="25 N+2"></asp:Label>
                    </asp:TableCell>
                </asp:TableRow>

                <asp:TableRow runat="server">
                    <asp:TableCell Width="20%">
                        Conditions:
                    </asp:TableCell>
                    <asp:TableCell Width="20%">
                        <asp:Label ID="beforeToCondCtl" runat="server" Text="Snow"></asp:Label>
                    </asp:TableCell>
                    <asp:TableCell Width="20%">
                        <asp:Label ID="toCondCtl" runat="server" Text="Calm"></asp:Label>
                    </asp:TableCell>
                    <asp:TableCell Width="20%">
                        <asp:Label ID="beforeFromCondCtl" runat="server" Text="Cloudy"></asp:Label>
                    </asp:TableCell>
                    <asp:TableCell Width="20%">
                        <asp:Label ID="fromCondCtl" runat="server" Text="Rain"></asp:Label>
                    </asp:TableCell>
                </asp:TableRow>

                <asp:TableRow runat="server">
                    <asp:TableCell Width="20%">
                        Precip. in/hr.:
                    </asp:TableCell>
                    <asp:TableCell Width="20%">
                        <asp:Label ID="beforeToPrecipRateCtl" runat="server" Text="0"></asp:Label>
                    </asp:TableCell>
                    <asp:TableCell Width="20%">
                        <asp:Label ID="toPrecipRateCtl" runat="server" Text="0.1"></asp:Label>
                    </asp:TableCell>
                    <asp:TableCell Width="20%">
                        <asp:Label ID="beforeFromPrecipRateCtl" runat="server" Text="1.0"></asp:Label>
                    </asp:TableCell>
                    <asp:TableCell Width="20%">
                        <asp:Label ID="fromPrecipRateCtl" runat="server" Text="0"></asp:Label>
                    </asp:TableCell>
                </asp:TableRow>

                <asp:TableRow runat="server">
                    <asp:TableCell Width="20%">
                        Humidity %:
                    </asp:TableCell>
                    <asp:TableCell Width="20%">
                        <asp:Label ID="beforeToHumCtl" runat="server" Text="85"></asp:Label>
                    </asp:TableCell>
                    <asp:TableCell Width="20%">
                        <asp:Label ID="toHumCtl" runat="server" Text="70"></asp:Label>
                    </asp:TableCell>
                    <asp:TableCell Width="20%">
                        <asp:Label ID="beforeFromHumCtl" runat="server" Text="50"></asp:Label>
                    </asp:TableCell>
                    <asp:TableCell Width="20%">
                        <asp:Label ID="fromHumCtl" runat="server" Text="0"></asp:Label>
                    </asp:TableCell>
                </asp:TableRow>


            </asp:Table>

        </div>


    
        <div class="jumbotron">
             <asp:Table ID="Table2" width="100%" runat="server">
                <asp:TableHeaderRow>
                    <asp:TableHeaderCell Width="20%"><i>**TOMORROW</i></asp:TableHeaderCell>
                    <asp:TableHeaderCell Width="20%"></asp:TableHeaderCell>
                    <asp:TableHeaderCell Width="20%">TO commute</asp:TableHeaderCell>
                    <asp:TableHeaderCell Width="20%"></asp:TableHeaderCell>
                    <asp:TableHeaderCell Width="20%">FROM commute</asp:TableHeaderCell>
                </asp:TableHeaderRow>
            
                <asp:TableRow runat="server">
                    <asp:TableCell Width="20%">
                        Temp. F:
                    </asp:TableCell>
                    <asp:TableCell Width="20%">
                        <asp:Label ID="beforeToTemp2Ctl" runat="server" Text="0"></asp:Label>
                    </asp:TableCell>
                    <asp:TableCell Width="20%">
                        <asp:Label ID="toTemp2Ctl" runat="server" Text="-1"></asp:Label>
                    </asp:TableCell>
                    <asp:TableCell Width="20%">
                        <asp:Label ID="beforeFromTemp2Ctl" runat="server" Text="-12"></asp:Label>
                    </asp:TableCell>
                    <asp:TableCell Width="20%">
                        <asp:Label ID="fromTemp2Ctl" runat="server" Text="5"></asp:Label>
                    </asp:TableCell>
                </asp:TableRow>
                <asp:TableRow runat="server">
                    <asp:TableCell Width="20%">
                        Wind mph:
                    </asp:TableCell>
                    <asp:TableCell Width="20%">
                        <asp:Label ID="beforeToWind2Ctl" runat="server" Text="12 NNE"></asp:Label>
                    </asp:TableCell>
                    <asp:TableCell Width="20%">
                        <asp:Label ID="toWind2Ctl" runat="server" Text="11 N"></asp:Label>
                    </asp:TableCell>
                    <asp:TableCell Width="20%">
                        <asp:Label ID="beforeFromWind2Ctl" runat="server" Text="9 SW"></asp:Label>
                    </asp:TableCell>
                    <asp:TableCell Width="20%">
                        <asp:Label ID="fromWind2Ctl" runat="server" Text="25 N"></asp:Label>
                    </asp:TableCell>
                </asp:TableRow>

                <asp:TableRow runat="server">
                    <asp:TableCell Width="20%">
                        Conditions:
                    </asp:TableCell>
                    <asp:TableCell Width="20%">
                        <asp:Label ID="beforeToCond2Ctl" runat="server" Text="Snow"></asp:Label>
                    </asp:TableCell>
                    <asp:TableCell Width="20%">
                        <asp:Label ID="toCond2Ctl" runat="server" Text="Calm"></asp:Label>
                    </asp:TableCell>
                    <asp:TableCell Width="20%">
                        <asp:Label ID="beforeFromCond2Ctl" runat="server" Text="Cloudy"></asp:Label>
                    </asp:TableCell>
                    <asp:TableCell Width="20%">
                        <asp:Label ID="fromCond2Ctl" runat="server" Text="Rain"></asp:Label>
                    </asp:TableCell>
                </asp:TableRow>

                <asp:TableRow runat="server">
                    <asp:TableCell Width="20%">
                        Precip. in/hr.:
                    </asp:TableCell>
                    <asp:TableCell Width="20%">
                        <asp:Label ID="beforeToPrecipRate2Ctl" runat="server" Text="5.0"></asp:Label>
                    </asp:TableCell>
                    <asp:TableCell Width="20%">
                        <asp:Label ID="toPrecipRate2Ctl" runat="server" Text="4.0"></asp:Label>
                    </asp:TableCell>
                    <asp:TableCell Width="20%">
                        <asp:Label ID="beforeFromPrecipRate2Ctl" runat="server" Text="1.0"></asp:Label>
                    </asp:TableCell>
                    <asp:TableCell Width="20%">
                        <asp:Label ID="fromPrecipRate2Ctl" runat="server" Text="0.0"></asp:Label>
                    </asp:TableCell>
                </asp:TableRow>

                <asp:TableRow runat="server">
                    <asp:TableCell Width="20%">
                        Humidity %:
                    </asp:TableCell>
                    <asp:TableCell Width="20%">
                        <asp:Label ID="beforeToHum2Ctl" runat="server" Text="85"></asp:Label>
                    </asp:TableCell>
                    <asp:TableCell Width="20%">
                        <asp:Label ID="toHum2Ctl" runat="server" Text="70"></asp:Label>
                    </asp:TableCell>
                    <asp:TableCell Width="20%">
                        <asp:Label ID="beforeFromHum2Ctl" runat="server" Text="50"></asp:Label>
                    </asp:TableCell>
                    <asp:TableCell Width="20%">
                        <asp:Label ID="fromHum2Ctl" runat="server" Text="0"></asp:Label>
                    </asp:TableCell>
                </asp:TableRow>


            </asp:Table>

    </div>

    <div class="row">
    </div>

</asp:Content>
