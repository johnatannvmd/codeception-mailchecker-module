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
	mkdir /usr/local/lathermail && \
	cd /usr/local/lathermail && \
	pip install lathermail && \
	apk del tzdata python-dev build-base openssl ca-certificates && \
	rm -rf /var/cache/apk/*

#		API  SMTP
EXPOSE	5000 2525

WORKDIR /usr/local/lathermail
ENTRYPOINT ["lathermail", "--api-host", "0.0.0.0", "--api-port", "5000", "--smtp-host", "0.0.0.0", "--smtp-port", "2525"]