using ProductLaunch.Entities;
using System.Data.Entity;

namespace ProductLaunch.Model.Initializers
{
    public class StaticDataInitializer : CreateDatabaseIfNotExists<ProductLaunchContext>
    {
        protected override void Seed(ProductLaunchContext context)
        {
            AddRole(context, "DA", "Developer Advocate");
            AddRole(context, "DM", "Decision Maker");
            AddRole(context, "AC", "Architect");
            AddRole(context, "EN", "Engineer");
            AddRole(context, "OP", "IT Ops");

            AddCountry(context, "GBR", "United Kingdom");
            AddCountry(context, "USA", "United States");
            AddCountry(context, "SWE", "Sweden");

            context.SaveChanges();
        }

        private void AddCountry(ProductLaunchContext context, string code, string name)
        {
            context.Countries.Add(new Country
            {
                CountryCode = code,
                CountryName = name
            });
        }

        private void AddRole(ProductLaunchContext context, string code, string name)
        {
            context.Roles.Add(new Role
            {
                RoleCode = code,
                RoleName = name
            });
        }
    }
}
