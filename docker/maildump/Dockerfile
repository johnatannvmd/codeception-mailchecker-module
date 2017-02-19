# Use Alpine Linux
FROM	python:2.7-alpine

ENV	TIMEZONE GMT

RUN	apk --no-cache add --virtual .build-dependencies build-base python-dev openssl ca-certificates && \
	python -m ensurepip && \
	mkdir /usr/local/maildump && \
	cd /usr/local/maildump && \
	pip install maildump && \
	apk del .build-dependencies && \
	rm -rf /var/cache/apk && \
	rm -rf /var/lib/apk && \
	rm -rf /etc/apk/cache

#		API  SMTP
EXPOSE	1080 1025

WORKDIR /usr/local/maildump
ENTRYPOINT ["maildump", "--http-ip", "0.0.0.0", "--http-port", "1080", "--smtp-ip", "0.0.0.0", "--smtp-port", "1025"]
