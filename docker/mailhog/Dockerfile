# Use Alpine Linux
FROM	alpine:latest

ENV	TIMEZONE GMT
ENV GOPATH /usr/local/mailhog

RUN	apk update && \
	apk upgrade && \
	apk add tzdata go git && \
	cp /usr/share/zoneinfo/${TIMEZONE} /etc/localtime && \
	echo "${TIMEZONE}" > /etc/timezone && \
	mkdir /usr/local/mailhog && \
	cd /usr/local/mailhog && \
	go get github.com/mailhog/MailHog && \
	apk del tzdata git go && \
	rm -rf /var/cache/apk/*

#		API  SMTP
EXPOSE	8025 1025

WORKDIR /usr/local/mailhog
ENTRYPOINT ["./bin/MailHog", "-api-bind-addr", "0.0.0.0:8025", "-smtp-bind-addr", "0.0.0.0:1025", "-storage", "memory"]
