using ProductLaunch.Entities;
using System.Data.Entity;

namespace ProductLaunch.Model
{
    public class ProductLaunchContext : DbContext
    {
        public ProductLaunchContext() : base("ProductLaunchDb") { }

        public DbSet<Country> Countries { get; set; }

        public DbSet<Role> Roles { get; set; }

        public DbSet<Prospect> Prospects { get; set; }

        protected override void OnModelCreating(DbModelBuilder builder)
        {
            builder.Entity<Country>().HasKey(c => c.CountryCode);
            builder.Entity<Role>().HasKey(r => r.RoleCode);
            builder.Entity<Prospect>().HasOptional<Country>(p => p.Country);
            builder.Entity<Prospect>().HasOptional<Role>(p => p.Role);            
        }        
    }
}
