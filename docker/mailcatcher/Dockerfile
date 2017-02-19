# Use Alpine Linux
FROM	alpine:latest

ENV	TIMEZONE GMT

RUN apk add --no-cache --virtual .build-dependencies tzdata ruby-dev ruby-rdoc ruby-irb sqlite-dev build-base ca-certificates && \
	apk add ruby ruby-json sqlite-libs libstdc++ && \
	cp /usr/share/zoneinfo/${TIMEZONE} /etc/localtime && \
	echo "${TIMEZONE}" > /etc/timezone && \
	mkdir /usr/local/mailcatcher && \
	cd /usr/local/mailcatcher && \
	gem install bigdecimal && \
	gem install mailcatcher && \
	apk del .build-dependencies && \
	rm -rf /var/cache/apk && \
	rm -rf /var/lib/apk && \
	rm -rf /etc/apk/cache

#		API  SMTP
EXPOSE	1080 1025

WORKDIR /usr/local/mailcatcher
ENTRYPOINT ["mailcatcher", "--http-ip", "0.0.0.0", "--http-port", "1080", "--smtp-ip", "0.0.0.0", "--smtp-port", "1025", "-f"]
