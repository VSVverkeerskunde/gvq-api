missing=()

echo 'Searching for missing strict_types...'

for i in $(find src tests -type f -name '*.php'); do
    missing+=(`head -n 1 ${i} | (grep -q '<?php declare(strict_types=1);' || echo ${i});`)
done

length=${#missing[@]}

if [ "$length" -gt 0 ]; then
    echo 'Found missing strict_types in the following files:'
    for i in "${missing[@]}"
    do
        echo "$i"
    done
    exit 1;
fi

echo 'No missing strict_types found.'
exit 0;
