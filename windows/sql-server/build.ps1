
# Part 1 - build the dacpac
docker build -t assets-db-builder -f Dockerfile.builder .
rmdir -Force -Recurse out
mkdir out
docker run --rm -v $pwd\out:c:\bin -v $pwd\src:c:\src assets-db-builder

# Part 2 - build the SQL server image
docker build -t assets-db .