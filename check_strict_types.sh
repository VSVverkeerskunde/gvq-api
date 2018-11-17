missing=()
RED='\033[0;31m'
NC='\033[0m'

printf 'Searching for missing strict_types...\n'

for i in $(find src tests -type f -name '*.php' -not -name '.bootstrap.php'); do
    missing+=(`head -n 1 ${i} | (grep -q '<?php declare(strict_types=1);' || echo ${i});`)
done

length=${#missing[@]}

if [ "$length" -gt 0 ]; then
    printf "${RED}Found missing strict_types in the following files:\n${NC}"
    for i in "${missing[@]}"
    do
        printf "$i\n"
    done
    printf '\n'
    exit 1;
fi

printf 'No missing strict_types found.'
printf '\n\n'
exit 0;
