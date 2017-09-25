CREATE TABLE [dbo].[Assets]
(
	[AssetId] INT NOT NULL PRIMARY KEY IDENTITY, 
    [AssetTypeId] INT NOT NULL, 
    [LocationId] INT NOT NULL, 
    [PurchaseDate] DATE NULL, 
    [PurchasePrice] DECIMAL(9, 2) NULL, 
    [AssetTag] NVARCHAR(50) NULL, 
    [AssetDescription] NVARCHAR(50) NULL, 
    [OwnerUserId] INT NULL, 
    CONSTRAINT [FK_Assets_To_AssetTypes] FOREIGN KEY ([AssetTypeId]) REFERENCES [AssetTypes]([AssetTypeId]),
	CONSTRAINT [FK_Assets_To_Locations] FOREIGN KEY ([LocationId]) REFERENCES [Locations]([LocationId]), 
    CONSTRAINT [FK_Assets_To_Users] FOREIGN KEY ([OwnerUserId]) REFERENCES [Users]([UserId])
)
