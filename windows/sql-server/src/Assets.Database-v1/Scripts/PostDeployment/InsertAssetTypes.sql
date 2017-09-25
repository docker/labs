IF NOT EXISTS (SELECT TOP 1 1 FROM AssetTypes)
BEGIN

    INSERT INTO [dbo].[AssetTypes] (AssetTypeDescription)
    VALUES ('Laptop')

    INSERT INTO [dbo].[AssetTypes] (AssetTypeDescription)
    VALUES ('Monitor')

    INSERT INTO [dbo].[AssetTypes] (AssetTypeDescription)
    VALUES ('Phone')

END
GO