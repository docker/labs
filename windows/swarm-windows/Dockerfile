# build windows swarm executable image
# adapted from https://stefanscherer.github.io/build-docker-swarm-for-windows-the-docker-way/

FROM windowsservercore:10.0.14300.1000

# install chocolatey pkg mgr
RUN @powershell -NoProfile -ExecutionPolicy unrestricted -Command "(iex ((new-object net.webclient).DownloadString('https://chocolatey.org/install.ps1'))) >$null 2>&1" && SET PATH=%PATH%;%ALLUSERSPROFILE%\chocolatey\bin

ENV SWARM_VERSION v1.2.2
ENV GOROOT C:\\tools\\go
ENV GOPATH C:\\work
ENV SWARM_HOST :2375

WORKDIR /

# copy utility bat script
COPY . .

# install golang, git, godep
# build swarm windows exe
# cleanup to reduce image layer size
RUN install-and-cleanup

ENTRYPOINT ["\\bin\\swarm.exe"]

CMD ["--help"]
