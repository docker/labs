<%@ Page Title="Home Page" Language="C#" MasterPageFile="~/Site.Master" AutoEventWireup="true" CodeBehind="Default.aspx.cs" Inherits="ProductLaunch.Web._Default" %>

<asp:Content ID="BodyContent" ContentPlaceHolderID="MainContent" runat="server">

    <div class="jumbotron">
        <h1>We&#39;re launching a new product!</h1>
        <p class="lead">Our new product is going to be very, very good.</p>
    </div>

    <div class="row">
        <div class="col-md-8">
            <h2>What&#39;s this all about?</h2>
            <p>
                Lorem ipsum dolor sit amet lectus. In magna in praesent nibh lorem. Egestas ipsum luctus feugiat sit enim. Libero nec a. Praesent vestibulum quis enim.</p>
            <p>
                Morbi fusce placerat et pellentesque qui curabitur dictum nam. Adipiscing pede semper. Tellus at sem. Arcu nibh et. Magna luctus nibh. Eu erat aenean adipiscing vitae pretium. Pede nec laoreet. Adipiscing mauris lorem tortor nec massa distinctio pede justo. Gravida non purus nunc sit consequat imperdiet sodales nullam dolor vel.</p>
            <p>
                <a class="btn btn-default" href="https://www.docker.com/enterprise">Check Out Our Other Products &raquo;</a>
            </p>
        </div>
        <div class="col-md-4">
            <h2>Interested?</h2>
            <p>
                Give us your details and we&#39;ll keep you posted.</p>
            <p>
                It only takes 30 seconds to sign up.
            </p>
            <p>
                And we probably won't spam you very much.
            </p>
            <p>
                <a class="btn btn btn-primary btn-lg" href="SignUp.aspx">Sign Up &raquo;</a>
            </p>
        </div>
    </div>

</asp:Content>
