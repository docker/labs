FROM microsoft/dotnet-framework:4.7.2-sdk-windowsservercore-ltsc2016
RUN nuget install Microsoft.Data.Tools.Msbuild -Version 10.0.61804.210