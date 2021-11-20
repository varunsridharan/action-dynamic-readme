#!/bin/bash
set -e

source /gh-toolkit/shell.sh

gh_log ""

gitconfig "Dynamic Readme" "githubactionbot+dynamicreadme@gmail.com"

gh_validate_input "FILES" "FILES List is required"

mkdir -p /dynamic-readme-tmp/repos/

if [ -z "$GITHUB_TOKEN" ]; then
  gh_log_error "🚩 Set the GITHUB_TOKEN env variable"
fi

if [ -z "$REPOSITORY_SLUG" ]; then
  gh_log "ℹ︎ Please Use https://github.com/varunsridharan/action-repository-meta Action to expose useful variables"
fi

RAW_FILES=$(gh_input "FILES")
FILES=($RAW_FILES)

GIT_URL="https://x-access-token:${GITHUB_TOKEN}@github.com/${GITHUB_REPOSITORY}.git"

php /dynamic-readme/global-repo.php

gh_log ""

for FILE in "${FILES[@]}"; do
  FILE=($(echo $FILE | tr "=" "\n"))
  SRC_FILE=${FILE[0]}
  gh_log_group_start "📓  ${SRC_FILE}"
  if [ ${FILE[1]+yes} ]; then
    DEST_FILE="${FILE[1]}"
  else
    DEST_FILE="${SRC_FILE}"
  fi

  DEST_FOLDER_PATH=$(dirname "${GITHUB_WORKSPACE}/${DEST_FILE}")

  if [ ! -d "$DEST_FOLDER_PATH" ]; then
    gh_log "  Creating [$DEST_FOLDER_PATH]"
    mkdir -p $DEST_FOLDER_PATH
  fi

  gh_log "SRC_FILE : ${SRC_FILE}"
  gh_log "DEST_FILE : ${DEST_FILE}"

  php /dynamic-readme/app.php "${SRC_FILE}" "${DEST_FILE}"
  gh_log ""

  git add -A
  git status

  if [ "$(git status --porcelain)" != "" ]; then
    git commit -m "💬 - File Rebuilt | Github Action Runner : ${GITHUB_RUN_NUMBER}"
  else
    gh_log "  ✅ No Changes Are Done : ${SRC_FILE}"
  fi
  gh_log_group_end
done
gh_log ""
if [ false ]; then
  git push $GIT_URL
fi
gh_log ""
