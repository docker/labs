# escape=`
FROM microsoft/nanoserver
SHELL ["powershell", "-Command", "$ErrorActionPreference = 'Stop';"]

EXPOSE 5000
ENV REGISTRY_STORAGE_FILESYSTEM_ROOTDIRECTORY=c:\\data

WORKDIR c:\\registry
COPY ./registry/ .

CMD ["registry", "serve", "config.yml"]