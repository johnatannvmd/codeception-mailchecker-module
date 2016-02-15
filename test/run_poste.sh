#!/usr/bin/env sh

docker run --name poste -p 1028:25 -p 8083:80 -p 443:443 -p 4110:110 -p 4143:143 -p 4465:465 -p 4587:587 -p 4993:993 -p 4995:995 -t johnatannvmd/poste > poste.out 2>&1 &

#
# check Poste is running
#
timeout=30
echo -n "Waiting ($timeout sec) for Poste availability..."
while ! ( php test.php > test.out ); do
    sleep 1
    echo -n "."
    timeout=$((timeout-1))
    if [ $timeout -eq 0 ]; then
      echo " timeout, exiting"
      cat poste.out
      echo "Test connection message:"
      cat test.out
      exit 1
    fi
done
echo " done!"

echo "Poste container log:"
cat poste.out

echo "Docker info:"
docker ps
