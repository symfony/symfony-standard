# -*- mode: ruby -*-
# vi: set ft=ruby :

# -*- mode: ruby -*-
# vi: set ft=ruby :

Vagrant::Config.run do |config|
  config.vm.define :main do |main_config|
    main_config.vm.box = "liip-squeeze64"
    main_config.vm.box_url = "http://vagrantbox.liip.ch/liip-squeeze64.box"
    main_config.vm.network :hostonly, "192.168.22.22"
    main_config.vm.forward_port 3306, 13306
    main_config.vm.share_folder "v-root", "/vagrant", ".", :nfs => true
    main_config.vm.provision :puppet, :module_path => "vagrant/puppet/modules" do |puppet|
      puppet.manifests_path = "vagrant/puppet/manifests"
      puppet.manifest_file = "main.pp"
    end
  end
end

