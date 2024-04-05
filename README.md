### Step 1: Preparation
Fololow instruction from https://github.com/yarsoniy/bills-k8s/blob/main/README.md

### Step 2: Clone this repository and install using Helm:
```
helm install bills-be ./helm/ --values ./helm/values.yaml -n bills
```

## Local development mode
### Install devspace
https://www.devspace.sh/docs/getting-started/installation

### Switch devspace to the correct k8s context and namespace
```
#enter and select from the list
devspace use context 

#enter and select from the list
devspace use namespace
```

### To start a devspace session
This will replace the normal pod to a despace pod
```
devspace dev
```

### To cleanup
```
devspace purge
```

