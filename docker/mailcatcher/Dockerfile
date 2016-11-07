# Use Alpine Linux
FROM	alpine:latest

ENV	TIMEZONE GMT

RUN	apk update && \
	apk upgrade && \
	apk add tzdata && \
	apk add ruby ruby-json ruby-dev ruby-rdoc ruby-irb sqlite-libs sqlite-dev build-base ca-certificates && \
	cp /usr/share/zoneinfo/${TIMEZONE} /etc/localtime && \
	echo "${TIMEZONE}" > /etc/timezone && \
	mkdir /usr/local/mailcatcher && \
	cd /usr/local/mailcatcher && \
	gem install bigdecimal && \
	gem install mailcatcher && \
	apk del tzdata ruby-dev ruby-rdoc ruby-irb sqlite-dev ca-certificates && \
	rm -rf /var/cache/apk/*

#		API  SMTP
EXPOSE	1080 1025

WORKDIR /usr/local/mailcatcher
ENTRYPOINT ["mailcatcher", "--http-ip", "0.0.0.0", "--http-port", "1080", "--smtp-ip", "0.0.0.0", "--smtp-port", "1025", "-f"]
