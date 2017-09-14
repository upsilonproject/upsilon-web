#!groovy                                                                           

properties(
	[
		buildDiscarder(logRotator(artifactDaysToKeepStr: '', artifactNumToKeepStr: '', daysToKeepStr: '', numToKeepStr: '10')), 
		[$class: 'CopyArtifactPermissionProperty', projectNames: '*'], 
		pipelineTriggers([[$class: 'PeriodicFolderTrigger', interval: '1d']])
	]
)

def prepareEnv() {
	deleteDir()                                                                    
                                                                                   
    unstash 'binaries'                                                             
                                                                                   
    env.WORKSPACE = pwd()                                                          
                                                                                   
    sh "find ${env.WORKSPACE}"                                                     

	sh 'mkdir -p SPECS SOURCES'                                                    
    sh "cp build/distributions/*.zip SOURCES/upsilon-web.zip"                      
}

def buildDockerContainer() {
	prepareEnv()
	unstash 'el7scl'

	sh 'mv RPMS/noarch/*.rpm RPMS/noarch/upsilon-web.rpm'

	sh 'unzip -jo SOURCES/upsilon-web.zip "upsilon-web-*/var/pkg/Dockerfile" "upsilon-web-*/.buildid" -d . '

	tag = sh script: 'buildid -pk tag', returnStdout: true

	println "tag: ${tag}"

	sh "docker build -t 'upsilonproject/web:${tag}' ."
	sh "docker tag 'upsilonproject/web:${tag}' 'upsilonproject/web:latest' "
	sh "docker save upsilonproject/web:${tag} > upsilon-web-docker-${tag}.tgz"

	archive "upsilon-web-docker-${tag}.tgz"
}
                                                                                  
def buildRpm(dist) {                                                               
	prepareEnv()                                                                                      
                                                                                      
    sh 'unzip -jo SOURCES/upsilon-web.zip "upsilon-web-*/setup/upsilon-web.spec" "upsilon-web-*/.buildid.rpmmacro" -d SPECS/'
    sh "find ${env.WORKSPACE}"                                                     
                                                                                   
    sh "rpmbuild -ba SPECS/upsilon-web.spec --define '_topdir ${env.WORKSPACE}' --define 'dist ${dist}'"
                                                                                   
    archive 'RPMS/noarch/*.rpm'                                                    
	stash includes: "RPMS/noarch/*.rpm", name: dist
}                                                                                  
                                                                                   
node {                                                                             
    stage "Prep"                                                                   
                                                                                   
    deleteDir()                                                                    
    def gradle = tool 'gradle'                                                     
                                                                                   
    checkout scm                                                                   
                                                                                   
    stage "Compile"                                                                
    sh "${gradle}/bin/gradle setupLibraries buildDojo phpUnit distZip"                                              
                                                                                   
	archive 'build/distributions/*.zip'
    stash includes:"build/distributions/*.zip", name: "binaries"                   
}                                                                                  
                                                                                   
node {                                                                             
    stage "Smoke"                                                                  
    echo "Smokin' :)"                                                              
}                                                                                  
                                                                                   
stage ("Package") {                                                          
	node {                                                                             
		buildRpm("el6")                                                                
	}
	node {                                                                             
		buildRpm("el7")                                                                
	}                                                                                  
	node {
		buildRpm("el7scl")
		buildDockerContainer()
	}
	node {                                                                             
		buildRpm("fc24")
	}                                                                                  
}
