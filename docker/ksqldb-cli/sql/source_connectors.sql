CREATE SOURCE CONNECTOR `postgres-source` WITH(
    "connector.class"='io.confluent.connect.jdbc.JdbcSourceConnector',
    "connection.url"='jdbc:postgresql://db:5432/lottery?user=user&password=pass',
    "mode"='timestamp',
    "timestamp.column.name"='created_at',
    "topic.prefix"='lottery_',
    "table.whitelist"='v1.award',
    "key"='id');