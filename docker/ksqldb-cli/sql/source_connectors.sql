CREATE SOURCE CONNECTOR `postgres-source` WITH(
    "connector.class"='io.confluent.connect.jdbc.JdbcSourceConnector',
    "connection.url"='jdbc:postgresql://db:5432/lottery?user=user&password=pass',
    "mode"='timestamp',
    "timestamp.column.name"='created_at',
    "topic.prefix"='kafka_',
    "table.whitelist"='lottery_award',
    "key"='id');