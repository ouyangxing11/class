FROM alpine
RUN sed -i 's/dl-cdn.alpinelinux.org/mirrors.aliyun.com/g' /etc/apk/repositories \
  && apk add  gcc g++ libc-dev  wget vim  openssl-dev make  linux-headers \
  && rm -rf /var/cache/apk/*

EXPOSE 6379
#通过选择更小的镜像，删除不必要文件清理不必要的安装缓存，从而瘦身镜像
#创建相关目录能够看到日志信息跟数据跟配置文件
RUN mkdir -p /usr/src/redis && mkdir -p /usr/src/redis/data &&  mkdir -p /usr/src/redis/conf  && mkdir -p /var/log/redis
RUN wget  -O /usr/src/redis/redis-4.0.11.tar.gz  "http://download.redis.io/releases/redis-4.0.11.tar.gz" \
   && tar -xzf /usr/src/redis/redis-4.0.11.tar.gz  -C /usr/src/redis \
   && rm -rf /usr/src/redis/redis-4.0.11.tar.tgz
RUN cd /usr/src/redis/redis-4.0.11 &&  make && make PREFIX=/usr/local/redis install \
&& ln -s /usr/local/redis/bin/*  /usr/local/bin/  && rm -rf /usr/src/redis/redis-4.0.11
COPY ./conf/redis.conf  /usr/src/redis/conf
CMD ["/usr/local/bin/redis-server","/usr/src/redis/conf/redis.conf"]
