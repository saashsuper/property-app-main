# Enable running x86_64 binaries on ARM64 using QEMU
RUN apt-get update && apt-get install -y \
    qemu-user-static \
    binfmt-support \
    && dpkg --add-architecture amd64 \
    && apt-get update \
    && apt-get install -y libc6:amd64 \
    && update-binfmts --enable qemu-x86_64 \
    && rm -rf /var/lib/apt/lists/*
