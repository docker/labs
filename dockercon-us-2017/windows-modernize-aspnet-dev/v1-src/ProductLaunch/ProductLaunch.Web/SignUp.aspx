<%@ Page Title="Sign Up" Language="C#" MasterPageFile="~/Site.Master" AutoEventWireup="true" CodeBehind="SignUp.aspx.cs" Inherits="ProductLaunch.Web.SignUp" %>

<asp:Content ID="BodyContent" ContentPlaceHolderID="MainContent" runat="server">

    <div class="jumbotron">
        <h1>Sign me up!</h1>
    </div>

    <div class="row">
        <div class="col-md-12">
            <h2>Just a few details</h2>
        </div>
    </div>

    <div class="form-group">
        <label for="txtFirstName">First Name</label>
        <asp:TextBox class="form-control" id="txtFirstName" runat="server"/>
    </div>
    <div class="form-group">
        <label for="txtLastName">Last Name</label>
        <asp:TextBox class="form-control" id="txtLastName" runat="server"/>
    </div>
    <div class="form-group">
        <label for="txtEmail">Email Address</label>
        <asp:TextBox class="form-control" id="txtEmail" runat="server" />
    </div>
    <div class="form-group">
        <label for="ddlCountry">Country</label>
        <asp:DropDownList class="form-control" id="ddlCountry" runat="server"/>
    </div>
    <div class="form-group">
        <label for="txtCompanyName">Company Name</label>
        <asp:TextBox class="form-control" id="txtCompanyName" runat="server"/>
    </div>
    <div class="form-group">
        <label for="ddlRole">Your Main Role</label>
        <asp:DropDownList class="form-control" id="ddlRole" runat="server" />
    </div>

    <asp:Button class="btn btn-default" runat="server" Text="Go!" ID="btnGo" OnClick="btnGo_Click" />

</asp:Content>
