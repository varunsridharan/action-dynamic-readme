<p align="center"><img src="https://cdn.svarun.dev/gh/actions.png" width="150px"/></p>

# Dynamic ReadMe - ***Github Action***
 Convert Static Readme into Dynamic Readme 

## Motivation
As an open-source software developer I use GitHub Repositories extensively to store my projects. I maintain over 100 projects, of which, about 85% of them have standardised content for the README.md file. That being said, I am finding it increasingly tedious to add, update or remove content in the README.md files across all my repositories because of two main challenges:

1. Templating of files: The information which is common to README.md files across all my repositories such as Sponsor, Contribute, Contact, etc., cannot be templated and inserted into the README.md files of all my projects / repositories.

2. Project / Repository-specific information: Github does not provide any repository-specific variables which can be used to dynamically insert repository information into the README.md file. As a result, repository-specific information needs to be hard-coded into the README file.

### Solution:
To overcome this limitation, and help developers such as myself automate this tedious task, I have created a GitHub action called “Github Action Dynamic ReadMe”. This action pulls repository-specific variables and also allows for templating of files, thereby easily creating dynamic file content for files such as README.md.


## ⚙️ Configuration
<!-- START RAW_CONTENT -->
| Option | Description | Default |
| --- | --- | --- |
| `FILES` | list of files that should be compiled.  | `false`
| `DELIMITER` | you can change the default **DELIMITER** if it causes issue with your data.  | `${{ }}`
| `GLOBAL_TEMPLATE_REPOSITORY` | you can set a global repository template where all the files are stored. | `false`
| `committer_name` | Specify the committer name | `Dynamic Readme` |
| `committer_email` | Specify the committer email | `githubactionbot+dynamicreadme@gmail.com` |
| `commit_message` | set a custom commit message | ` 💬 - File Rebuilt | Github Action Runner : ${GITHUB_RUN_NUMBER} ` |
| `confirm_and_push` | Commit the changes and push directly to repository | `true` |
<!-- END RAW_CONTENT -->

## :writing_hand: Syntax 
<!-- START RAW_CONTENT -->
* Variables : `${{ VARIABLE_NAME }}`
* Inline File Includes : `<!-- include {filepath} -->`
* Reusable  File Includes :
    * Start : `<!-- START include {filepath} -->`
    * END : `<!-- END include {filepath} -->`

### Variables
All Default vairables exposed by github actions runner can be accessed like `$‎{{ GITHUB_ACTIONS }}` OR  `$‎{{ GITHUB_ACTOR }}`
<!-- END RAW_CONTENT -->

**Dynamic Readme Github Action** Uses [**Repository Meta - Github Action**](https://github.com/varunsridharan/action-repository-meta) which 
exposes useful metadata as environment variable and those variables can be used as template tags.

any variables exposed by **Repository Meta** can be accessed like below

<!-- START RAW_CONTENT -->
```markdown
Repository Owner : ${{ env.REPOSITORY_OWNER }}
Repository Full Name : ${{ env.REPOSITORY_FULL_NAME }}
```

> :information_source: **Note :** Any environment variable can be accessed just by using `env.` as prefix `${{ env.VARIABLE_NAME }}`
<!-- END RAW_CONTENT -->

### File Includes
#### Source Options
* Relative Path : `template/file.md`
* Absolute path : `./template/file.md`
* From Repository : `{owner}/{repository}/{filepath}` OR `{owner}/{repository}@{branch}/{filepath}`

#### Relative Path Syntax 
Files are always searched from repository root path
<!-- START RAW_CONTENT -->
```markdown
Inline Includes : 
<!-- include template/file.md -->

Reusable Includes : 
<!-- START template/file.md -->

<!-- END template/file.md -->
```
<!-- END RAW_CONTENT -->

#### Absolute path Syntax 
Files are searched from current repository. This can come in handy when writing nested includesType a message
<!-- START RAW_CONTENT -->
```markdown
Inline Includes : 
<!-- include ./template/file.md -->

Reusable Includes : 
<!-- START ./template/file.md -->

<!-- END ./template/file.md -->
```
<!-- END RAW_CONTENT -->

#### From Repository Syntax 
You can include any type of file from any repository. If you want to include a file from a **Private Repository**, you have to provide **Github Personal Access** Token INSTEAD OF **Github Token** in the action's workflow file.
> :information_source: If branch is not specified then default branch will be cloned

##### Without Branch
<!-- START RAW_CONTENT -->
```markdown
Inline Includes : 
<!-- include octocat/Spoon-Knife/README.md -->

Reusable Includes : 
<!-- START octocat/Spoon-Knife/README.md -->

<!-- END octocat/Spoon-Knife/README.md -->
```
<!-- END RAW_CONTENT -->

##### Custom Branch
<!-- START RAW_CONTENT -->
```markdown
Inline Includes : 
<!-- include octocat/Spoon-Knife/@master/README.md -->

Reusable Includes : 
<!-- START octocat/Spoon-Knife/@master/README.md -->

<!-- END octocat/Spoon-Knife/@master/README.md -->
```
<!-- END RAW_CONTENT -->

#### Builtin Markdown Handlers
blow options can be used with **inline** / **resuable** includes.

* code - `[code]` OR `[code:{code_type}]` -  will print the contents of the file inside a codeblock
* blockquote - `[blockquote]` -  will print the contents of the file inside a blockquote
* raw - `[raw]` OR `[escape]` - will print the actual contents without rendering it.

##### Example
<!-- START RAW_CONTENT -->
```markdown
Inline Includes With Custom Markdown Below file will be included inside HTML blockquote element
<!-- include [code] template/thankyou.md --> 

Inline Includes With Custom Markdown Below file will be included inside HTML blockquote element
<!-- include [code:json] template/contents.json --> 

Reusable Includes Custom Markdown :
<!-- START [code] template/file.md -->
<!-- END [code] template/file.md -->

Reusable Includes Custom Markdown :
<!-- START [code:json] template/contents.json -->
<!-- END [code:json] template/contents.json -->
```
<!-- EMD RAW_CONTENT -->

> :information_source: **Inline includes** can come in handy when you want to parse the data once and save it. It can also be used inside a nested include.
>
> :information_source: Even though **Reusable includes** and **Inline Includes** do the same work, they can come in handy when you are generating a template and saving it in the same file. It preserves the include comment which will be parsed again when re-generating the template, and the contents of the include will be updated accordingly.

###  Pull Request Support

This action modifies the files, creates a commit and uploads them directly to the default branch of the repository.

If the default branch of the repository is protected by the rule `Require a pull request before merging` then you will need to set the option `confirm_and_push: false` so that the commit is not made and the changes are not uploaded to the default branch.

``` yaml
- name: "💫  Dynamic Template Render"
  uses: varunsridharan/action-dynamic-readme@main
  with:
    GLOBAL_TEMPLATE_REPOSITORY: {repository-owner}/{repository-name}
    files: |
      FILE.md
      FILE2.md=output_filename.md
      folder1/file.md=folder2/output.md
    confirm_and_push: false # Important if use "Require a pull request before merging" rule
  env:
    GITHUB_TOKEN: ${{ secrets.GITHUB_TOKEN }}
```

In addition to the previous configuration, it is necessary to use other actions to be able to confirm the changes and generate a Pull Request towards the default branch

``` yaml
- name: "📥  Fetching Repository Contents"
  uses: actions/checkout@main

- name: "💫  Dynamic Template Render"
  uses: varunsridharan/action-dynamic-readme@main
  with:
    GLOBAL_TEMPLATE_REPOSITORY: {repository-owner}/{repository-name}
    files: |
      FILE.md
      FILE2.md=output_filename.md
      folder1/file.md=folder2/output.md
    confirm_and_push: false # Important if use "Require a pull request before merging" rule
  env:
    GITHUB_TOKEN: ${{ secrets.GITHUB_TOKEN }}

- name: Create pull request
  uses: peter-evans/create-pull-request@v3
  with:
    token: ${{ secrets.GITHUB_TOKEN }}
```

Thanks to this configuration, the necessary files can be dynamically generated by adding them to a different branch that then we can merge with the default branch, complying with the rule `Require a pull request before merging`.

Check out the example workflow (Pull Request Support) for a more advanced configuration

---

<h3 align="center"> For live Demo Please Check <a href="https://github.com/varunsridharan/demo-dynamic-readme">Demo Repository</a> </h3>

---

## 🚀 Example Workflow File
<!-- START RAW_CONTENT -->
```yaml
name: Dynamic Template

on:
  push:
    branches:
      - main
  workflow_dispatch:

jobs:
  update_templates:
    name: "Update Templates"
    runs-on: ubuntu-latest
    steps:
      - name: "📥  Fetching Repository Contents"
        uses: actions/checkout@main

      - name: "💾  Github Repository Metadata"
        uses: varunsridharan/action-repository-meta@main
        env:
          GITHUB_TOKEN: ${{ secrets.GITHUB_TOKEN }}

      - name: "💫  Dynamic Template Render"
        uses: varunsridharan/action-dynamic-readme@main
        with:
          GLOBAL_TEMPLATE_REPOSITORY: {repository-owner}/{repository-name}
          files: |
            FILE.md
            FILE2.md=output_filename.md
            folder1/file.md=folder2/output.md
        env:
          GITHUB_TOKEN: ${{ secrets.GITHUB_TOKEN }}
```

<!-- END RAW_CONTENT -->

## 🚀 Example Workflow File (Pull Request Support)

<!-- START RAW_CONTENT -->
```yaml
name: Dynamic Template

on:
  push:
    branches:
      - main
  workflow_dispatch:

jobs:
  update_templates:
    name: "Update Templates"
    runs-on: ubuntu-latest
    steps:
      - name: "📥  Fetching Repository Contents"
        uses: actions/checkout@main

      - name: "💾  Github Repository Metadata"
        uses: varunsridharan/action-repository-meta@main
        env:
          GITHUB_TOKEN: ${{ secrets.GITHUB_TOKEN }}

      - name: "💫  Dynamic Template Render"
        uses: varunsridharan/action-dynamic-readme@main
        with:
          GLOBAL_TEMPLATE_REPOSITORY: {repository-owner}/{repository-name}
          files: |
            FILE.md
            FILE2.md=output_filename.md
            folder1/file.md=folder2/output.md
          committer_name: github-actions[bot]
          committer_email: github-actions[bot]@users.noreply.github.com
          commit_message: 'docs: update readme file [skip ci]'
          confirm_and_push: false
        env:
          GITHUB_TOKEN: ${{ secrets.GITHUB_TOKEN }}

      - name: Create pull request
        id: cpr
        uses: peter-evans/create-pull-request@v3
        with:
          token: ${{ secrets.GITHUB_TOKEN }}
          commit-message: 'docs: documentation files updated [skip ci]'
          committer: github-actions[bot] <github-actions[bot]@users.noreply.github.com>
          author: github-actions[bot] <github-actions[bot]@users.noreply.github.com>
          signoff: false
          branch: github-actions/repository-maintenance
          delete-branch: true
          title: Update documentation files
          body: All documentation files has been updated to last recent version
          labels: |
            skip-changelog
            docs

      - name: Enable pull request automerge
        if: steps.cpr.outputs.pull-request-operation == 'created'
        uses: peter-evans/enable-pull-request-automerge@v1
        with:
          token: ${{ secrets.PAT }}
          pull-request-number: ${{ steps.cpr.outputs.pull-request-number }}

      - name: Auto approve
        if: steps.cpr.outputs.pull-request-operation == 'created'
        uses: juliangruber/approve-pull-request-action@v1
        with:
          github-token: ${{ secrets.PAT }}
          number: ${{ steps.cpr.outputs.pull-request-number }}
```

<!-- END RAW_CONTENT -->

---

<!-- START common-footer.mustache -->
## 📝 Changelog
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

[Checkout CHANGELOG.md](https://github.com/varunsridharan/action-dynamic-readme/blob/main/CHANGELOG.md)


## 🤝 Contributing
If you would like to help, please take a look at the list of [issues](https://github.com/varunsridharan/action-dynamic-readme/issues/).


## 📜  License & Conduct
- [**MIT License**](https://github.com/varunsridharan/action-dynamic-readme/blob/main/LICENSE) © [Varun Sridharan](website)
- [Code of Conduct](https://github.com/varunsridharan/.github/blob/master/CODE_OF_CONDUCT.md)


## 📣 Feedback
- ⭐ This repository if this project helped you! :wink:
- Create An [🔧 Issue](https://github.com/varunsridharan/action-dynamic-readme/issues/) if you need help / found a bug


## 💰 Sponsor
[I][twitter] fell in love with open-source in 2013 and there has been no looking back since! You can read more about me [here][website].
If you, or your company, use any of my projects or like what I’m doing, kindly consider backing me. I'm in this for the long run.

- ☕ How about we get to know each other over coffee? Buy me a cup for just [**$9.99**][buymeacoffee]
- ☕️☕️ How about buying me just 2 cups of coffee each month? You can do that for as little as [**$9.99**][buymeacoffee]
- 🔰         We love bettering open-source projects. Support 1-hour of open-source maintenance for [**$24.99 one-time?**][paypal]
- 🚀         Love open-source tools? Me too! How about supporting one hour of open-source development for just [**$49.99 one-time ?**][paypal]

<!-- Personl Links -->
[paypal]: https://sva.onl/paypal
[buymeacoffee]: https://sva.onl/buymeacoffee
[twitter]: https://sva.onl/twitter/
[website]: https://sva.onl/website/


## Connect & Say 👋
- **Follow** me on [👨‍💻 Github][github] and stay updated on free and open-source software
- **Follow** me on [🐦 Twitter][twitter] to get updates on my latest open source projects
- **Message** me on [📠 Telegram][telegram]
- **Follow** my pet on [Instagram][sofythelabrador] for some _dog-tastic_ updates!

<!-- Personl Links -->
[sofythelabrador]: https://www.instagram.com/sofythelabrador/
[github]: https://sva.onl/github/
[twitter]: https://sva.onl/twitter/
[telegram]: https://sva.onl/telegram/


---

<p align="center">
<i>Built With ♥ By <a href="https://sva.onl/twitter"  target="_blank" rel="noopener noreferrer">Varun Sridharan</a> <a href="https://en.wikipedia.org/wiki/India">
   <img src="https://cdn.svarun.dev/flag-india.jpg" width="20px"/></a> </i> <br/><br/>
   <img src="https://cdn.svarun.dev/codeispoetry.png"/>
</p>

---


<!-- END common-footer.mustache -->