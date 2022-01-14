#!/bin/bash

if [ 'Linux' == "$(uname)" ]; then
  OS_UID=$(id -u)
  OS_GID=$(id -g)
else
  OS_UID=1000
  OS_GID=1000
fi

echo "export OS_UID=${OS_UID}
export OS_GID=${OS_GID}\
" > ./env/docker
