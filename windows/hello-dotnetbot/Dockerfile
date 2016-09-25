FROM microsoft/dotnet:latest

WORKDIR /root/  
ADD ./app/ ./app/  
WORKDIR /root/app/

RUN dotnet restore  
RUN dotnet build

CMD ["dotnet", "run", "Hello World"]  
