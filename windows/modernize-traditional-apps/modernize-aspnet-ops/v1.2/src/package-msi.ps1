$wixPath = "C:\Program Files (x86)\WiX Toolset v3.10\bin"

& "$wixPath\candle.exe"  -out .\Output\UpgradeSample.wixobj .\UpgradeSample.Setup\Product.wxs -ext WixIIsExtension -ext WixUiExtension -ext WixUtilExtension
& "$wixPath\light.exe" -out .\Output\UpgradeSample.msi .\Output\UpgradeSample.wixobj -ext WixIISExtension -ext WixUiExtension  -ext WixUtilExtension -cultures:en-us 
