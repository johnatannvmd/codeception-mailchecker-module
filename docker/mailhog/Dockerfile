# Use Alpine Linux
FROM	alpine:latest

ENV	TIMEZONE GMT
ENV GOPATH /usr/local/mailhog

RUN	apk add --no-cache --virtual .build-dependencies tzdata go git ca-certificates build-base && \
	cp /usr/share/zoneinfo/${TIMEZONE} /etc/localtime && \
	echo "${TIMEZONE}" > /etc/timezone && \
	mkdir /usr/local/mailhog && \
	cd /usr/local/mailhog && \
	go get github.com/mailhog/MailHog && \
	apk del .build-dependencies && \
	rm -rf /var/cache/apk && \
	rm -rf /var/lib/apk && \
	rm -rf /etc/apk/cache

#		API  SMTP
EXPOSE	8025 1025

WORKDIR /usr/local/mailhog
ENTRYPOINT ["./bin/MailHog", "-api-bind-addr", "0.0.0.0:8025", "-smtp-bind-addr", "0.0.0.0:1025", "-storage", "memory"]
