# Use Alpine Linux
FROM	python:2.7-alpine

ENV	TIMEZONE GMT

RUN	apk add --no-cache --virtual .build-dependencies python python-dev openssl ca-certificates build-base && \
	python -m ensurepip && \
	mkdir /usr/local/lathermail && \
	cd /usr/local/lathermail && \
	pip install lathermail && \
	apk del .build-dependencies && \
	rm -rf /root/.cache/pip && \
	rm -rf /var/cache/apk && \
	rm -rf /var/lib/apk && \
	rm -rf /etc/apk/cache

#		API  SMTP
EXPOSE	5000 2525

WORKDIR /usr/local/lathermail
ENTRYPOINT ["lathermail", "--api-host", "0.0.0.0", "--api-port", "5000", "--smtp-host", "0.0.0.0", "--smtp-port", "2525"]
