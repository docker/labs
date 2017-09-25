CREATE TABLE [dbo].[Locations]
(
	[LocationId] INT NOT NULL PRIMARY KEY IDENTITY, 
    [Country] NVARCHAR(50) NOT NULL, 
    [PostalCode] NVARCHAR(50) NULL, 
    [AddressLine1] NVARCHAR(500) NULL
)
