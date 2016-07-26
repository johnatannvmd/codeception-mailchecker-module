# Use Alpine Linux
FROM	alpine:latest

ENV	TIMEZONE GMT

RUN	apk update && \
	apk upgrade && \
	apk add tzdata && \
	apk add python python-dev openssl ca-certificates build-base && \
	apk add py-pip && \
	cp /usr/share/zoneinfo/${TIMEZONE} /etc/localtime && \
	echo "${TIMEZONE}" > /etc/timezone && \
	mkdir /usr/local/maildump && \
	cd /usr/local/maildump && \
	pip install maildump && \
	apk del tzdata python-dev build-base openssl ca-certificates && \
	rm -rf /var/cache/apk/*

#		API  SMTP
EXPOSE	1080 1025

WORKDIR /usr/local/maildump
ENTRYPOINT ["maildump", "--http-ip", "0.0.0.0", "--http-port", "1080", "--smtp-ip", "0.0.0.0", "--smtp-port", "1025"]