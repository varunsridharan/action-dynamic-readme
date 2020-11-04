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

---

<!-- START common-footer.mustache -->

<!-- END common-footer.mustache -->