
# Part 1 - build the dacpac
docker image build -t assets-db-builder -m 2GB -f Dockerfile.builder .
rmdir -Force -Recurse out
mkdir out
docker run --rm -v $pwd\out:c:\bin -v $pwd\src:c:\src assets-db-builder

# Part 2 - build the SQL server image
docker build -t assets-db .