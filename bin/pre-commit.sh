#!/bin/bash

# Сохраняем выводы psalm и php_codesniffer в переменные
PSALM_OUTPUT=$(vendor/bin/psalm)
PHPCS_OUTPUT=$(vendor/bin/phpcs)

# Проверяем, есть ли предупреждения в выводе psalm
if [[ $PSALM_OUTPUT == *"No errors!"* ]]; then
  echo "Psalm: No errors detected."
else
  echo "Psalm: Errors detected. Please fix before committing."
  echo "$PSALM_OUTPUT"
  exit 1
fi

# Проверяем, есть ли предупреждения в выводе php_codesniffer
if [[ $PHPCS_OUTPUT == *"No errors found"* ]]; then
  echo "PHP_CodeSniffer: No errors detected."
else
  echo "PHP_CodeSniffer: Errors detected. Please fix before committing."
  echo "$PHPCS_OUTPUT"
  exit 1
fi

# Если все проверки успешны, разрешаем коммит
exit 0