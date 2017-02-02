using System;
using Microsoft.VisualStudio.TestTools.UnitTesting;
using ProductLaunch.Entities;

namespace ProductLaunch.Model.Tests
{
    [TestClass]
    public class ProductLaunchContextTest
    {
        [TestMethod]
        public void Insert()
        {
            using (var context = new ProductLaunchContext())
            {
                var country = new Country
                {
                    CountryCode = "GBR",
                    CountryName = "United Kingdom"
                };
                context.Countries.Add(country);

                var role = new Role
                {
                    RoleCode = "DM",
                    RoleName = "Decision Maker"
                };
                context.Roles.Add(role);

                var prospect = new Prospect
                {
                    FirstName = "A",
                    LastName = "Prospect",
                    CompanyName = "Docker, Inc.",
                    EmailAddress = "a.prospect@docker.com",
                    Country = country,
                    Role = role
                };
                context.Prospects.Add(prospect);
                context.SaveChanges();
            }
        }
    }
}
