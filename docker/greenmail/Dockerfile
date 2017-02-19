# Use Alpine Linux
FROM	alpine:latest

ENV	TIMEZONE GMT

RUN	apk add --update openjdk8-jre-base tzdata && \
	cp /usr/share/zoneinfo/${TIMEZONE} /etc/localtime && \
	echo "${TIMEZONE}" > /etc/timezone && \
	apk del tzdata && \
	rm -rf /var/cache/apk/* && \
	mkdir /usr/local/greenmail && \
	cd /usr/local/greenmail && \
	wget http://central.maven.org/maven2/com/icegreen/greenmail-standalone/1.5.3/greenmail-standalone-1.5.3.jar

#		SMTP POP3 IMAP SMTP IMAP POP3
EXPOSE	3025 3110 3143 3465 3993 3995

WORKDIR /usr/local/greenmail
ENTRYPOINT ["/usr/bin/java", \
    "-Djava.net.preferIPv4Stack=true", \
    "-Dgreenmail.setup.test.all", \
    "-Dgreenmail.hostname=0.0.0.0", \
    "-Dgreenmail.auth.disabled", \
    "-jar", "greenmail-standalone-1.5.3.jar"]
