
Here is the sequential steps that we go through when first demoing Che - and emphasizing Docker.
https://eclipse-che.readme.io/docs/che-and-multi-machine-workspaces

Key elements of any demo:
1. Starting che using docker - showing that we have a really simple syntax.
 `docker run --rm -t -v /var/run/docker.sock:/var/run/docker.sock eclipse/che start` http://localhost:8080/
 https://eclipse-che.readme.io/docs/usage-docker
 Problem ^^ seems to need an external ip
2. Starting a compose-based workspace, and showing that it's multiple containers with code mapped into it.
3. Getting into the code and showing intellisense.
4. Setting a breakpoint and launching a debugger.
5. Using commands to build and run, with the debugger


