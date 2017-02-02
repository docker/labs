using Microsoft.VisualStudio.TestTools.UnitTesting;
using OpenQA.Selenium;
using OpenQA.Selenium.Firefox;
using OpenQA.Selenium.Support.UI;
using System;
using System.Threading;
using TechTalk.SpecFlow;

namespace ProductLaunch.EndToEndTests
{
    [Binding]
    public class ProspectSignUpSteps
    {
        private static IWebDriver _Driver;

        [BeforeFeature]
        public static void Setup()
        {
            _Driver = new FirefoxDriver();
        }

        [AfterFeature]
        public static void TearDown()
        {
            _Driver.Close();
            _Driver.Dispose();
        }

        [Given(@"I browse to the Sign Up Page at ""(.*)""")]
        public void GivenIBrowseToTheSignUpPageAt(string host)
        {
            var url = $"http://{host}/SignUp";            
            _Driver.Navigate().GoToUrl(url);
        }
        
        [Given(@"I enter details '(.*)' '(.*)' '(.*)' '(.*)' '(.*)' '(.*)'")]
        public void GivenIEnterDetails(string firstName, string lastName, string emailAddress, 
                                       string companyName, string country, string role)
        {            
            _Driver.FindElement(By.Id("MainContent_txtFirstName")).SendKeys(firstName);
            _Driver.FindElement(By.Id("MainContent_txtLastName")).SendKeys(lastName);
            _Driver.FindElement(By.Id("MainContent_txtEmail")).SendKeys(emailAddress);
            _Driver.FindElement(By.Id("MainContent_txtCompanyName")).SendKeys(companyName);

            new SelectElement(_Driver.FindElement(By.Id("MainContent_ddlCountry"))).SelectByText(country);
            new SelectElement(_Driver.FindElement(By.Id("MainContent_ddlRole"))).SelectByText(role);
        }

        [When(@"I press Go")]
        public void WhenIPressGo()
        {
            var goButton = _Driver.FindElement(By.Id("MainContent_btnGo"));
            goButton.Click();
        }
        
        [Then(@"I should see the Thank You page")]
        public void ThenIShouldSeeTheThankYouPage()
        {
            //HACK
            Thread.Sleep(1500);
            Assert.AreEqual("Ta", _Driver.Title);
        }
    }
}
